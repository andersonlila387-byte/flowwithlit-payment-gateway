<?php
require_once __DIR__ . '/PrivateStorage.php';

/**
 * Onboarding for both account types. Anything collected at signup (name, email,
 * phone, country, business name, business stage) is read from the user record and
 * never asked again; it is copied into the payload so admin review still sees it.
 *
 *  - Personal  ("individual"): date of birth, home address, ID document.
 *  - Business: business details (+ registration for registered businesses),
 *              owner identity and address, payout account.
 *
 * Both kinds land in onboarding_submissions for admin review. Personal
 * submissions have no business_id.
 */
class OnboardingService {
    public const FILE_FIELDS = ['businessLogo', 'identityDocument', 'addressDocument', 'registrationDocument', 'bankStatement'];
    public const INDUSTRIES = ['Retail & e-commerce', 'Food & hospitality', 'Fashion & beauty', 'Technology & software', 'Education', 'Health & wellness', 'Travel & transport', 'Logistics & delivery', 'Professional services', 'Media & entertainment', 'Real estate', 'Agriculture', 'Non-profit & religious', 'Other'];
    private const STAGE_TO_TYPE = ['starter' => 'Personal business', 'registered' => 'Registered company'];
    private const IDENTITY_LIMITS = ['dateOfBirth' => 10, 'addressLine1' => 191, 'city' => 100, 'stateRegion' => 100, 'phone' => 30];
    private const BUSINESS_LIMITS = ['businessName' => 191, 'businessStage' => 12, 'industry' => 60, 'website' => 255, 'registrationNumber' => 40, 'accountName' => 191, 'bankName' => 120, 'bankCode' => 6, 'bankVerificationToken' => 64, 'accountNumber' => 10];

    /** Signup details for the signed-in user, used to prefill and to skip repeated questions. */
    public static function profile(PDO $pdo, int $userId): array {
        $query = $pdo->prepare('SELECT u.*, p.date_of_birth, p.address_line1, p.city, p.state_region, p.country_code, c.name AS country_name
            FROM users u LEFT JOIN user_profiles p ON p.user_id = u.id LEFT JOIN countries c ON c.code = p.country_code WHERE u.id = ?');
        $query->execute([$userId]);
        $u = $query->fetch(PDO::FETCH_ASSOC) ?: [];
        return [
            'account_type' => ($u['account_type'] ?? '') === 'business' ? 'business' : 'individual',
            'full_name' => (string) ($u['full_name'] ?? ''),
            'first_name' => $u['first_name'] ?? null,
            'last_name' => $u['last_name'] ?? null,
            'email' => (string) ($u['email'] ?? ''),
            'phone' => $u['phone'] ?? null,
            'country_code' => $u['country_code'] ?? null,
            'country_name' => $u['country_name'] ?? null,
            'business_name' => $u['signup_business_name'] ?? null,
            'business_stage' => $u['business_stage'] ?? null,
            'date_of_birth' => $u['date_of_birth'] ?? null,
            'address_line1' => $u['address_line1'] ?? null,
            'city' => $u['city'] ?? null,
            'state_region' => $u['state_region'] ?? null,
            'kyc_tier' => (int) ($u['kyc_tier'] ?? 1),
        ];
    }

    private static function text(array $input, string $name, int $limit): string {
        $value = $input[$name] ?? '';
        if (!is_string($value) || mb_strlen(trim($value)) > $limit) throw new DomainException('Invalid value for ' . $name . '.');
        return trim($value);
    }

    public static function validate(array $input, bool $submit, array $profile): array {
        $isBusiness = $profile['account_type'] === 'business';
        $data = [];
        foreach (self::IDENTITY_LIMITS as $name => $limit) $data[$name] = self::text($input, $name, $limit);
        if ($isBusiness) foreach (self::BUSINESS_LIMITS as $name => $limit) $data[$name] = self::text($input, $name, $limit);

        // Contact details come from the account, not the form.
        $data['ownerName'] = $profile['full_name'];
        $data['ownerEmail'] = $profile['email'];
        if (!empty($profile['phone'])) $data['phone'] = $profile['phone'];
        elseif ($data['phone'] !== '' && !preg_match('/^\+?[0-9 ()-]{7,30}$/D', $data['phone'])) throw new DomainException('Enter a valid phone number.');

        if ($data['dateOfBirth'] !== '') {
            $dob = DateTimeImmutable::createFromFormat('!Y-m-d', $data['dateOfBirth']);
            if (!$dob || $dob->format('Y-m-d') !== $data['dateOfBirth']) throw new DomainException('Enter a valid date of birth.');
            $age = $dob->diff(new DateTimeImmutable('today'))->y;
            if ($dob > new DateTimeImmutable('today') || $age < 18) throw new DomainException('You must be at least 18 years old to open an account.');
            if ($age > 120) throw new DomainException('Enter a valid date of birth.');
        }

        if ($isBusiness) {
            // Business stage was chosen at signup; only accounts created before that ask it here.
            if (!empty($profile['business_stage'])) $data['businessStage'] = $profile['business_stage'];
            elseif ($data['businessStage'] !== '' && !isset(self::STAGE_TO_TYPE[$data['businessStage']])) throw new DomainException('Choose starter or registered business.');
            $data['businessType'] = self::STAGE_TO_TYPE[$data['businessStage']] ?? '';
            if ($data['industry'] !== '' && !in_array($data['industry'], self::INDUSTRIES, true)) throw new DomainException('Choose your industry from the list.');
            if ($data['website'] !== '') {
                if (!preg_match('#^https?://#i', $data['website'])) $data['website'] = 'https://' . $data['website'];
                if (!filter_var($data['website'], FILTER_VALIDATE_URL)) throw new DomainException('Enter a valid website or social media link.');
            }
            if ($data['businessStage'] !== 'registered') $data['registrationNumber'] = '';
            if ($data['accountNumber'] !== '' && !preg_match('/^[0-9]{10}$/D', $data['accountNumber'])) throw new DomainException('The payout account number must contain 10 digits.');
            if ($data['bankCode'] !== '' && !preg_match('/^[0-9]{3,6}$/D', $data['bankCode'])) throw new DomainException('Select your bank from the list.');
        }

        $data['declaration'] = ($input['declaration'] ?? '') === '1';
        if ($submit) {
            $required = ['dateOfBirth' => 'your date of birth', 'addressLine1' => 'your home address', 'city' => 'your city', 'stateRegion' => 'your state or region', 'phone' => 'your phone number'];
            if ($isBusiness) {
                $required += ['businessStage' => 'your business type', 'businessName' => 'your business name', 'industry' => 'your industry', 'accountName' => 'the payout account name', 'bankName' => 'the payout bank', 'accountNumber' => 'the payout account number'];
                if ($data['businessStage'] === 'registered') $required['registrationNumber'] = 'your CAC registration number';
            }
            foreach ($required as $field => $label) if (($data[$field] ?? '') === '') throw new DomainException('Please add ' . $label . '.');
            if (!$data['declaration']) throw new DomainException('Please confirm the declaration before submitting.');
            $data['declarationVersion'] = '2026-11-16';
        }
        return $data;
    }

    /** Documents that must be on file before a submission is accepted. */
    public static function requiredFiles(array $profile, array $data): array {
        if ($profile['account_type'] !== 'business') return ['identityDocument' => 'a valid ID document'];
        $files = ['identityDocument' => "the owner's ID document", 'addressDocument' => 'a proof of address'];
        if (($data['businessStage'] ?? '') === 'registered') $files['registrationDocument'] = 'your CAC registration certificate';
        return $files;
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
        $profile = self::profile($pdo, $userId);
        $query = $pdo->prepare('SELECT * FROM onboarding_submissions WHERE user_id=?');
        $query->execute([$userId]); $row = $query->fetch();
        if (!$row) return ['status'=>'draft','review_note'=>null,'data'=>new stdClass(),'files'=>new stdClass(),'profile'=>$profile];
        $data = json_decode(PrivateStorage::decrypt(base64_decode($row['payload_encrypted'], true), 'onboarding:' . $userId), true, 512, JSON_THROW_ON_ERROR);
        $query = $pdo->prepare('SELECT id,field_name,mime_type,byte_size FROM onboarding_files WHERE submission_id=?');
        $query->execute([$row['id']]); $files = [];
        foreach ($query->fetchAll() as $file) $files[$file['field_name']] = ['id'=>(int)$file['id'],'mime'=>$file['mime_type'],'size'=>(int)$file['byte_size']];
        return ['status'=>$row['status'],'review_note'=>$row['review_note'] ?? null,'data'=>$data,'files'=>$files ?: new stdClass(),'profile'=>$profile];
    }

    public static function save(PDO $pdo, int $userId, array $input, array $uploads, bool $submit): array {
        $profile = self::profile($pdo, $userId);
        $data = self::validate($input, $submit, $profile);
        if ($profile['account_type'] === 'business') $data = self::applyPayoutVerification($pdo, $userId, $data);
        $validated = [];
        foreach ($uploads as $field=>$file) {
            if (($file['error'] ?? null) === UPLOAD_ERR_NO_FILE) continue;
            if ($field === 'registrationDocument' && ($data['businessStage'] ?? '') !== 'registered') continue;
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
                foreach (self::requiredFiles($profile, $data) as $field => $label) {
                    if (empty($files[$field])) throw new DomainException('Please upload ' . $label . '.');
                }
                self::persistIdentity($pdo, $userId, $profile, $data);
                $businessId = $profile['account_type'] === 'business' ? self::persistBusiness($pdo, $userId, $row, $data, $files) : null;
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

    /**
     * Payout account check via Paystack's account-name lookup (BankVerificationService).
     *  - Fresh lookup token: the bank's own account name replaces whatever was typed → verified.
     *  - No token, same number and bank as an already-verified draft: stays verified (lookup tokens
     *    expire after 10 minutes, so the review step must not need a new one).
     *  - Otherwise (lookup unavailable or skipped): kept as typed and marked unverified for admins.
     */
    private static function applyPayoutVerification(PDO $pdo, int $userId, array $data): array {
        $token = $data['bankVerificationToken'];
        unset($data['bankVerificationToken']);
        $data['payoutVerified'] = false;
        if ($data['accountNumber'] === '' || $data['bankCode'] === '') return $data;
        if ($token !== '') {
            require_once __DIR__ . '/BankVerificationService.php';
            $destination = (new BankVerificationService($pdo))->verified($userId, $token, $data['accountNumber'], $data['bankCode']);
            $data['accountName'] = $destination['account_name'];
            $data['payoutVerified'] = true;
            return $data;
        }
        $previous = self::read($pdo, $userId)['data'];
        $previous = is_array($previous) ? $previous : [];
        if (!empty($previous['payoutVerified']) && ($previous['accountNumber'] ?? '') === $data['accountNumber'] && ($previous['bankCode'] ?? '') === $data['bankCode']) {
            $data['accountName'] = $previous['accountName'];
            $data['payoutVerified'] = true;
        }
        return $data;
    }

    /** Date of birth and address go on the profile; a phone is stored only if signup had none. */
    private static function persistIdentity(PDO $pdo, int $userId, array $profile, array $data): void {
        $pdo->prepare('INSERT INTO user_profiles (user_id, date_of_birth, address_line1, city, state_region) VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE date_of_birth = VALUES(date_of_birth), address_line1 = VALUES(address_line1), city = VALUES(city), state_region = VALUES(state_region)')
            ->execute([$userId, $data['dateOfBirth'], $data['addressLine1'], $data['city'], $data['stateRegion']]);
        if (empty($profile['phone'])) $pdo->prepare('UPDATE users SET phone=? WHERE id=?')->execute([$data['phone'], $userId]);
        if ($profile['account_type'] === 'business') {
            $pdo->prepare('UPDATE users SET business_stage=COALESCE(business_stage,?), signup_business_name=COALESCE(signup_business_name,?) WHERE id=?')
                ->execute([$data['businessStage'], $data['businessName'], $userId]);
        }
    }

    private static function persistBusiness(PDO $pdo, int $userId, array $row, array $data, array $files): int {
        $type = $data['businessStage'] === 'registered' ? 'limited_company' : 'sole_proprietor';
        $values = [$data['businessName'], null, $data['registrationNumber'] ?: null, $type, $data['industry'] ?: null, $data['website'] ?: null];
        if ($row['business_id']) {
            $businessId = (int)$row['business_id'];
            $pdo->prepare("UPDATE businesses SET legal_name=?,trading_name=?,registration_number=?,business_type=?,industry=?,website_url=?,verification_status='submitted' WHERE id=? AND owner_user_id=?")->execute([...$values,$businessId,$userId]);
        } else {
            $pdo->prepare("INSERT INTO businesses (legal_name,trading_name,registration_number,business_type,industry,website_url,uuid,owner_user_id,verification_status) VALUES (?,?,?,?,?,?,?,?,'submitted')")->execute([...$values,generateUUID(),$userId]);
            $businessId = (int)$pdo->lastInsertId();
        }
        $pdo->prepare("INSERT IGNORE INTO business_members (business_id,user_id,role) VALUES (?,?,'owner')")->execute([$businessId,$userId]);
        $pdo->prepare("DELETE FROM bank_accounts WHERE business_id=? AND user_id=? AND status='pending'")->execute([$businessId,$userId]);
        $account = base64_encode(PrivateStorage::encrypt($data['accountNumber'], 'bank:' . $userId));
        $pdo->prepare("INSERT INTO bank_accounts (user_id,business_id,bank_name,account_name,account_number_encrypted,account_number_last4,bank_code,currency_code,status) VALUES (?,?,?,?,?,?,?,'NGN','pending')")->execute([$userId,$businessId,$data['bankName'],$data['accountName'],$account,substr($data['accountNumber'],-4),$data['bankCode'] ?: null]);
        $pdo->prepare("DELETE FROM business_documents WHERE business_id=? AND status='uploaded'")->execute([$businessId]);
        $types = ['businessLogo'=>'other','identityDocument'=>'identity','addressDocument'=>'address_proof','registrationDocument'=>'registration','bankStatement'=>'bank_statement'];
        foreach ($files as $field=>$key) {
            if ($field === 'registrationDocument' && $data['businessStage'] !== 'registered') continue;
            if (isset($types[$field])) $pdo->prepare('INSERT INTO business_documents (business_id,document_type,file_key) VALUES (?,?,?)')->execute([$businessId,$types[$field],$key]);
        }
        return $businessId;
    }
}
