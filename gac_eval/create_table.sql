DROP TABLE IF EXISTS `appels`;
CREATE TABLE IF NOT EXISTS `appels` (
  `num_compte` varchar(10) NOT NULL COMMENT 'Compte facture',
  `num_fac` varchar(10) NOT NULL COMMENT 'Numero de facture',
  `num_abo` varchar(10) NOT NULL COMMENT 'Numero abonne',
  `date` date NOT NULL COMMENT 'date',
  `heure` time NOT NULL COMMENT 'heure',
  `temps_appel` float NOT NULL COMMENT 'duree/volume reel',
  `temps_facture` float NOT NULL COMMENT 'duree/volume facture',
  `datas_internet` float NOT NULL COMMENT 'datas_internet en Mo',
  `datas_internet_facture` float NOT NULL COMMENT 'Mo facture',
  `type` text NOT NULL COMMENT 'type'
) ENGINE=CSV DEFAULT CHARSET=utf8 COMMENT='Details des appels';
COMMIT;