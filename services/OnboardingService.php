<?php
require_once __DIR__ . '/PrivateStorage.php';

class OnboardingService {
    public const FILE_FIELDS = ['businessLogo', 'identityDocument', 'addressDocument', 'registrationDocument', 'bankStatement'];
    private const LIMITS = ['businessType'=>40,'businessName'=>191,'ownerName'=>150,'ownerEmail'=>191,'phone'=>30,'accountName'=>191,'bankName'=>120,'accountNumber'=>10];
    public static function validate(array $input, bool $submit): array {
        $data = [];
        foreach (self::LIMITS as $name => $limit) {
            $value = $input[$name] ?? '';
            if (!is_string($value) || mb_strlen(trim($value)) > $limit) throw new DomainException('Invalid value for ' . $name . '.');
            $data[$name] = trim($value);
        }
        foreach (['businessType'=>['Personal business','Registered company']] as $field=>$choices) {
            if ($data[$field] !== '' && !in_array($data[$field], $choices, true)) throw new DomainException('Please select a valid ' . $field . '.');
        }
        if ($data['ownerEmail'] !== '' && !filter_var($data['ownerEmail'], FILTER_VALIDATE_EMAIL)) throw new DomainException('Enter a valid owner email.');
        if ($data['phone'] !== '' && !preg_match('/^\+?[0-9 ()-]{7,30}$/D', $data['phone'])) throw new DomainException('Enter a valid phone number.');
        if ($data['accountNumber'] !== '' && !preg_match('/^[0-9]{10}$/D', $data['accountNumber'])) throw new DomainException('The payout account number must contain 10 digits.');
        $data['declaration'] = ($input['declaration'] ?? '') === '1';
        if ($submit) {
            foreach (self::LIMITS as $field => $_) if ($data[$field] === '') throw new DomainException('Please complete ' . $field . '.');
            if (!$data['declaration']) throw new DomainException('Please confirm the declaration before submitting.');
            $data['declarationVersion'] = '2026-09-19';
        }
        return $data;
    }
    public static function validateUpload(array $file, string $field): array {
        if (!in_array($field, self::FILE_FIELDS, true) || !isset($file['error']) || is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK) throw new DomainException('Upload failed for ' . $field . '. Please choose the file again.');
        $tmp = $file['tmp_name'] ?? '';
        if (!is_string($tmp) || !is_uploaded_file($tmp)) throw new DomainException('Invalid upload.');
        $size = filesize($tmp);
        if (!$size || $size > 10 * 1024 * 1024) throw new DomainException('Each file must be between 1 byte and 10MB.');
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
        $allowed = ['image/png','image/jpeg','image/webp'];
        if ($field !== 'businessLogo') $allowed[] = 'application/pdf';
        if (!in_array($mime, $allowed, true)) throw new DomainException('Unsupported file type for ' . $field . '.');
        if (str_starts_with($mime, 'image/') && !getimagesize($tmp)) throw new DomainException('Invalid image.');
        return ['tmp'=>$tmp, 'mime'=>$mime, 'size'=>$size];
    }
    public static function read(PDO $pdo, int $userId): array {
        $query = $pdo->prepare('SELECT * FROM onboarding_submissions WHERE user_id=?');
        $query->execute([$userId]); $row = $query->fetch();
        if (!$row) return ['status'=>'draft','data'=>new stdClass(),'files'=>new stdClass()];
        $data = json_decode(PrivateStorage::decrypt(base64_decode($row['payload_encrypted'], true), 'onboarding:' . $userId), true, 512, JSON_THROW_ON_ERROR);
        $query = $pdo->prepare('SELECT id,field_name,mime_type,byte_size FROM onboarding_files WHERE submission_id=?');
        $query->execute([$row['id']]); $files = [];
        foreach ($query->fetchAll() as $file) $files[$file['field_name']] = ['id'=>(int)$file['id'],'mime'=>$file['mime_type'],'size'=>(int)$file['byte_size']];
        return ['status'=>$row['status'],'data'=>$data,'files'=>$files ?: new stdClass()];
    }
    public static function save(PDO $pdo, int $userId, array $input, array $uploads, bool $submit): array {
        $data = self::validate($input, $submit);
        $validated = [];
        foreach ($uploads as $field=>$file) {
            if (($file['error'] ?? null) === UPLOAD_ERR_NO_FILE) continue;
            $validated[$field] = self::validateUpload($file, $field);
        }
        $newKeys = []; $oldKeys = [];
        try {
            $pdo->beginTransaction();
            $query = $pdo->prepare('SELECT id FROM users WHERE id=? FOR UPDATE'); $query->execute([$userId]);
            $query = $pdo->prepare('SELECT * FROM onboarding_submissions WHERE user_id=?'); $query->execute([$userId]); $row = $query->fetch();
            if ($row && in_array($row['status'], ['submitted','under_review','approved'], true)) {
                $pdo->commit(); return self::read($pdo, $userId);
            }
            $encrypted = base64_encode(PrivateStorage::encrypt(json_encode($data, JSON_THROW_ON_ERROR), 'onboarding:' . $userId));
            if (!$row) {
                $pdo->prepare('INSERT INTO onboarding_submissions (user_id,payload_encrypted) VALUES (?,?)')->execute([$userId,$encrypted]);
                $row = ['id'=>(int)$pdo->lastInsertId(),'business_id'=>null];
            }
            foreach ($validated as $field=>$file) {
                $key = PrivateStorage::put(file_get_contents($file['tmp'])); $newKeys[] = $key;
                $query = $pdo->prepare('SELECT storage_key FROM onboarding_files WHERE submission_id=? AND field_name=?'); $query->execute([$row['id'],$field]);
                if ($old = $query->fetchColumn()) $oldKeys[] = $old;
                $pdo->prepare('INSERT INTO onboarding_files (submission_id,field_name,storage_key,mime_type,byte_size) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE storage_key=VALUES(storage_key),mime_type=VALUES(mime_type),byte_size=VALUES(byte_size),created_at=NOW()')->execute([$row['id'],$field,$key,$file['mime'],$file['size']]);
            }
            if ($submit) {
                $query = $pdo->prepare('SELECT field_name,storage_key FROM onboarding_files WHERE submission_id=?'); $query->execute([$row['id']]); $files = $query->fetchAll(PDO::FETCH_KEY_PAIR);
                $businessId = self::persistBusiness($pdo, $userId, $row, $data, $files);
                $pdo->prepare("UPDATE onboarding_submissions SET payload_encrypted=?,business_id=?,status='submitted',submitted_at=NOW() WHERE id=?")->execute([$encrypted,$businessId,$row['id']]);
                $pdo->prepare('UPDATE users SET onboarding_completed_at=NOW() WHERE id=?')->execute([$userId]);
            } else {
                $pdo->prepare("UPDATE onboarding_submissions SET payload_encrypted=?,status='draft' WHERE id=?")->execute([$encrypted,$row['id']]);
            }
            $pdo->commit();
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            foreach ($newKeys as $key) PrivateStorage::remove($key);
            throw $error;
        }
        foreach ($oldKeys as $key) PrivateStorage::remove($key);
        return self::read($pdo, $userId);
    }
    private static function persistBusiness(PDO $pdo, int $userId, array $row, array $data, array $files): int {
        $type = $data['businessType'] === 'Registered company' ? 'limited_company' : 'sole_proprietor';
        $values = [$data['businessName'],null,null,$type,null,null];
        if ($row['business_id']) {
            $businessId = (int)$row['business_id'];
            $pdo->prepare("UPDATE businesses SET legal_name=?,trading_name=?,registration_number=?,business_type=?,industry=?,website_url=?,verification_status='submitted' WHERE id=? AND owner_user_id=?")->execute([...$values,$businessId,$userId]);
        } else {
            $pdo->prepare("INSERT INTO businesses (legal_name,trading_name,registration_number,business_type,industry,website_url,uuid,owner_user_id,verification_status) VALUES (?,?,?,?,?,?,?,?,'submitted')")->execute([...$values,generateUUID(),$userId]);
            $businessId = (int)$pdo->lastInsertId();
        }
        $pdo->prepare("INSERT IGNORE INTO business_members (business_id,user_id,role) VALUES (?,?,'owner')")->execute([$businessId,$userId]);
        $pdo->prepare('UPDATE users SET full_name=?,phone=? WHERE id=?')->execute([$data['ownerName'],$data['phone'],$userId]);
        $pdo->prepare("DELETE FROM bank_accounts WHERE business_id=? AND user_id=? AND status='pending'")->execute([$businessId,$userId]);
        $account = base64_encode(PrivateStorage::encrypt($data['accountNumber'], 'bank:' . $userId));
        $pdo->prepare("INSERT INTO bank_accounts (user_id,business_id,bank_name,account_name,account_number_encrypted,account_number_last4,currency_code,status) VALUES (?,?,?,?,?,?,'NGN','pending')")->execute([$userId,$businessId,$data['bankName'],$data['accountName'],$account,substr($data['accountNumber'],-4)]);
        $pdo->prepare("DELETE FROM business_documents WHERE business_id=? AND status='uploaded'")->execute([$businessId]);
        $types = ['businessLogo'=>'other','identityDocument'=>'identity','addressDocument'=>'address_proof','registrationDocument'=>'registration','bankStatement'=>'bank_statement'];
        foreach ($files as $field=>$key) if (isset($types[$field])) $pdo->prepare('INSERT INTO business_documents (business_id,document_type,file_key) VALUES (?,?,?)')->execute([$businessId,$types[$field],$key]);
        return $businessId;
    }
}
