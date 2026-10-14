ALTER TABLE `trans_skbs`
  ADD COLUMN IF NOT EXISTS `skbs_docnumb` varchar(100) DEFAULT NULL AFTER `skbs_doc_date`;
