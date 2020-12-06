21/11/20
ALTER TABLE `skus` ADD `is_active` TINYINT NOT NULL DEFAULT '1' AFTER `updated_at`;
ALTER TABLE `skus` ADD `deleted_at` DATETIME NULL AFTER `updated_at`;

22/11/20
ALTER TABLE `pickups` ADD `receiver_latitude` VARCHAR(100) NULL DEFAULT NULL AFTER `receiver_landmark`, ADD `receiver_longitude` VARCHAR(100) NULL DEFAULT NULL AFTER `receiver_latitude`;

ALTER TABLE `pickups` ADD `sender_latitude` VARCHAR(100) NULL DEFAULT NULL AFTER `sender_landmark`, ADD `sender_longitude` VARCHAR(100) NULL DEFAULT NULL AFTER `sender_latitude`;

ALTER TABLE `pickups` ADD `distance` VARCHAR(10) NULL DEFAULT NULL AFTER `weight`, ADD `cost_per_km` VARCHAR(10) NULL DEFAULT NULL AFTER `distance`, ADD `payable_amount` VARCHAR(10) NULL DEFAULT NULL AFTER `cost_per_km`;

27/11/20
ALTER TABLE `carrier` ADD `latitude` VARCHAR(100) NULL DEFAULT NULL AFTER `is_available`, ADD `longitude` VARCHAR(100) NULL DEFAULT NULL AFTER `latitude`;

2/12/20
ALTER TABLE `carrier` ADD `proof_vehicle_no` VARCHAR(255) NULL DEFAULT NULL AFTER `longitude`, ADD `proof_photo` VARCHAR(255) NULL DEFAULT NULL AFTER `proof_vehicle_no`, ADD `proof_address` VARCHAR(255) NULL DEFAULT NULL AFTER `proof_photo`;

CREATE TABLE `carrier_report` (
  `id` int(11) NOT NULL,
  `carrier_id` int(11) NOT NULL,
  `reported_proof_vehicle_no` tinyint(4) NOT NULL DEFAULT 0,
  `reported_proof_photo` tinyint(4) NOT NULL DEFAULT 0,
  `reported_proof_address` tinyint(4) NOT NULL DEFAULT 0,
  `remarks` varchar(255) DEFAULT NULL,
  `resolved` tinyint(4) NOT NULL DEFAULT 0,
  `raised_on` datetime DEFAULT NULL,
  `resolved_on` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carrier_report`
--
ALTER TABLE `carrier_report`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carrier_report`
--
ALTER TABLE `carrier_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
