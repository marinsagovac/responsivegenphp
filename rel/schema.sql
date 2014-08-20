--
-- Table structure for table `apidata`
--

CREATE TABLE IF NOT EXISTS `apidata` (
  `apidata_id` int(11) NOT NULL AUTO_INCREMENT,
  `apidata_version` varchar(2) COLLATE utf8_bin NOT NULL,
  `apidata_name` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`apidata_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `apidoc`
--

CREATE TABLE IF NOT EXISTS `apidoc` (
  `apidoc_id` int(11) NOT NULL AUTO_INCREMENT,
  `apidoc_parent_id` int(11) NOT NULL,
  `apidoc_name` varchar(40) COLLATE utf8_bin NOT NULL,
  `apidoc_version` varchar(2) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`apidoc_id`),
  KEY `apidoc_parent_id` (`apidoc_parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `apimenu`
--

CREATE TABLE IF NOT EXISTS `apimenu` (
  `apimenu_id` int(11) NOT NULL AUTO_INCREMENT,
  `apimenu_doc_parent_id` int(11) DEFAULT NULL,
  `apimenu_name` varchar(40) COLLATE utf8_bin NOT NULL,
  `apimenu_parent_id` int(11) DEFAULT NULL,
  `apimenu_menu_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `apimenu_menu_link` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`apimenu_id`),
  KEY `apimenu_doc_parent_id` (`apimenu_doc_parent_id`),
  KEY `apimenu_parent_id` (`apimenu_parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=44 ;

-- --------------------------------------------------------

--
-- Table structure for table `txtdata`
--

CREATE TABLE IF NOT EXISTS `txtdata` (
  `txtdata_id` int(11) NOT NULL DEFAULT '0',
  `txtdata_content` text COLLATE utf8_bin,
  PRIMARY KEY (`txtdata_id`),
  KEY `txtdata_id` (`txtdata_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `apidoc`
--
ALTER TABLE `apidoc`
  ADD CONSTRAINT `apidoc_ibfk_1` FOREIGN KEY (`apidoc_parent_id`) REFERENCES `apidata` (`apidata_id`);

--
-- Constraints for table `apimenu`
--
ALTER TABLE `apimenu`
  ADD CONSTRAINT `apimenu_ibfk_1` FOREIGN KEY (`apimenu_doc_parent_id`) REFERENCES `apidoc` (`apidoc_id`),
  ADD CONSTRAINT `apimenu_ibfk_2` FOREIGN KEY (`apimenu_parent_id`) REFERENCES `apimenu` (`apimenu_id`);

--
-- Constraints for table `txtdata`
--
ALTER TABLE `txtdata`
  ADD CONSTRAINT `txtdata_ibfk_1` FOREIGN KEY (`txtdata_id`) REFERENCES `apimenu` (`apimenu_id`);
