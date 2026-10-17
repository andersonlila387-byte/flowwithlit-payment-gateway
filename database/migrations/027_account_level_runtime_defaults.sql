-- Runtime enforcement must not depend on an administrator merely opening the Levels screen.
INSERT IGNORE INTO admin_account_levels (uuid,account_type,rank,name,requirement,deposit_single,deposit_daily,withdraw_single,withdraw_daily,transfer_single,transfer_daily,max_balance,fee_percent,api_access,payment_links,bulk_transfers) VALUES
(UUID(),'individual',1,'Starter','Email verified',50000,100000,25000,50000,50000,100000,0,1.5,0,0,0),
(UUID(),'individual',2,'Verified','Identity verified',250000,500000,150000,300000,250000,500000,500000,1,1,1,0),
(UUID(),'individual',3,'Advanced','Enhanced verification',1000000,2000000,750000,1500000,1000000,2000000,2000000,.75,1,1,1),
(UUID(),'business',1,'Business starter','Business profile approved',500000,1000000,250000,500000,500000,1000000,0,1.5,0,1,0),
(UUID(),'business',2,'Business verified','Business verification approved',2000000,5000000,1500000,3000000,2000000,5000000,5000000,1,1,1,1);
INSERT IGNORE INTO schema_migrations(version) VALUES ('027_account_level_runtime_defaults');
