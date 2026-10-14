<?php
declare(strict_types=1);

final class ApiRateLimit {
    /** Atomic fixed-window counters; never share the login lockout table. */
    public static function consume(PDO $db,string $scope,string $subject,int $limit,int $seconds):bool {
        $start=intdiv(time(),$seconds)*$seconds;$hash=hash('sha256',$subject);
        $q=$db->prepare('INSERT INTO api_rate_limits(scope,subject_hash,window_start,expires_at) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE attempts=attempts+1');
        $q->execute([$scope,$hash,$start,gmdate('Y-m-d H:i:s',$start+$seconds)]);
        $q=$db->prepare('SELECT attempts FROM api_rate_limits WHERE scope=? AND subject_hash=? AND window_start=?');$q->execute([$scope,$hash,$start]);
        return (int)$q->fetchColumn()<=$limit;
    }
}
