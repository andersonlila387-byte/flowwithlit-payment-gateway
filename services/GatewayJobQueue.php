<?php
declare(strict_types=1);

final class GatewayJobQueue {
    public static function enqueue(PDO $db,string $type,string $key,array $payload,int $delaySeconds=0):void {
        $q=$db->prepare("INSERT INTO gateway_jobs(job_key,job_type,payload,available_at) VALUES(?,?,?,DATE_ADD(UTC_TIMESTAMP(6),INTERVAL ? SECOND)) ON DUPLICATE KEY UPDATE job_key=job_key");
        $q->execute([$key,$type,json_encode($payload,JSON_THROW_ON_ERROR),$delaySeconds]);
    }
    public static function claim(PDO $db,string $worker):?array {
        $db->beginTransaction();
        try {
            $q=$db->prepare("SELECT * FROM gateway_jobs WHERE status='queued' AND available_at<=UTC_TIMESTAMP(6) ORDER BY id LIMIT 1 FOR UPDATE SKIP LOCKED");$q->execute();$job=$q->fetch();
            if(!$job){$db->commit();return null;}
            $db->prepare("UPDATE gateway_jobs SET status='running',attempts=attempts+1,locked_at=UTC_TIMESTAMP(6),locked_by=? WHERE id=?")->execute([$worker,$job['id']]);$db->commit();return $job;
        }catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
    }
    public static function complete(PDO $db,int $id):void{$db->prepare("UPDATE gateway_jobs SET status='completed',completed_at=UTC_TIMESTAMP(6),locked_at=NULL WHERE id=?")->execute([$id]);}
    public static function fail(PDO $db,int $id,string $error,int $attempts):void{$delay=min(3600,2 ** min($attempts,10));$db->prepare("UPDATE gateway_jobs SET status=IF(attempts>=12,'failed','queued'),available_at=DATE_ADD(UTC_TIMESTAMP(6),INTERVAL ? SECOND),last_error=?,locked_at=NULL WHERE id=?")->execute([$delay,mb_substr($error,0,500),$id]);}
}
