ALTER TABLE `sks`
  ADD COLUMN IF NOT EXISTS `patient_nik` varchar(20) DEFAULT NULL AFTER `patient_name`;
