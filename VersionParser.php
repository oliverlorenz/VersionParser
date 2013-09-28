<?php

/**
 * @author Oliver Lorenz
 * @since 2013-09-15
 *
 * Simple class for calculating und comparing version numbers
 */

class VersionParser 
{
	const BITS = 8;

	// Parts of a version number
	const MAJOR 			= 0;
	const MINOR 			= 1;
	const MAINTENENCE 		= 2;
	const BUILD 			= 3;
	const STATE				= 4;
	const SUBSTATEVERSION 	= 5;

	protected $_version = array();
	protected $_versionString = null;

	public function __construct($version)
	{
		$this->_versionString = $version;
		$this->_initVersion();
		$this->_parseCompleteVersionNumber($version);
	}

	/**
	 * returns original given version string
	 * @return string version number
	 */
	public function getOriginalVersionSting()
	{
		return $this->_versionString;
	}

	/**
	 * returns the diffrent parts of the version number
	 * @return array parts of the version
	 */
	public function getVersionParts() 
	{
		return $this->version;
	}

	protected function _initVersion()
	{
		$this->version[self::MAJOR] = 0;
		$this->version[self::MINOR] = 0;
		$this->version[self::MAINTENENCE] = 0;
		$this->version[self::BUILD] = 0;
		$this->version[self::STATE] = 0;
		$this->version[self::SUBSTATEVERSION] = 0;
	}

	/**
	 * return 
	 * @return [type] [description]
	 */
	protected function _getVersionPattern() {
		$patternParts = array();
		$patternParts[self::MAJOR] = 'v?(\d+)[.]?';
		$patternParts[self::MINOR] = '(\d+)?[.]?';
		$patternParts[self::MAINTENENCE] = '(\d+)?[.]?';
		$patternParts[self::BUILD] = '(\d+)?[.]?';
		$patternParts[self::STATE] = '[- ]?([a-z]{2,})?';
		$patternParts[self::SUBSTATEVERSION] = '[- ]?(\d+)?';
		$completePattern = implode('', $patternParts);
		return $completePattern;
	}

	public function getMajorVersion() 
	{
		return $this->version[self::MAJOR];
	}

	public function setMajorVersion($majorVersion)
	{
		$this->version[self::MAJOR] = $majorVersion;
	}

	public function setMinorVersion($minorVersion)
	{
		$this->version[self::MINOR] = $minorVersion;
	}

	public function getMinorVersion() 
	{
		return $this->version[self::MINOR];
	}

	public function getMaintenenceVersion() 
	{
		return $this->version[self::MAINTENENCE];
	}

	public function setMaintenenceVersion($maintenenceVersion)
	{
		$this->version[self::MAINTENENCE] = $maintenenceVersion;
	}
	
	public function getBuildVersion() 
	{
		return $this->version[self::BUILD];
	}

	public function setBuildVersion($buildVersion)
	{
		$this->version[self::BUILD] = $buildVersion;
	}
	
	public function getStateVersion() 
	{
		return $this->version[self::STATE];
	}

	public function setStateVersion($stateVersion)
	{
		$this->version[self::STATE] = $stateVersion;
	}
	
	public function getSubstateVersion() 
	{
		return $this->version[self::SUBSTATEVERSION];
	}

	public function setSubstateVersion($substateVersion)
	{
		$this->version[self::SUBSTATEVERSION] = $substateVersion;
	}
	
	public function equals(Version $version) {
		return $this->getInteger() === $version->getInteger();
	}

	public function greater(Version $version) {
		return $this->getInteger() > $version->getInteger();
	}

	public function lower(Version $version) {
		return $this->getInteger() < $version->getInteger();
	}

	public function isValid($version)
	{
		return !empty($this->_getMatches());
	}

	public function getNextMajorVersion()
	{
		$majorVersion = $this->getMajorVersion();
		$majorVersion++;
		return new Version($majorVersion);
	}

	public function getNextMinorVersion()
	{
		$majorVersion = $this->getMajorVersion();
		$minorVersion = $this->getMinorVersion();
		$minorVersion++;
		$versionNumber = $this->_getFullVersionNumber(
						 	 $majorVersion, 
						     $minorVersion
						 );
		return new Version($versionNumber);
	}

	public function getNextMaintenenceVersion()
	{
		$majorVersion = $this->getMajorVersion();
		$minorVersion = $this->getMinorVersion();
		$maintenenceVersion = $this->getMaintenenceVersion();
		$maintenenceVersion++;
		$versionNumber = $this->_getFullVersionNumber(
						 	 $majorVersion, 
						     $minorVersion,
						     $maintenenceVersion
						 );
		return new Version($versionNumber);
	}

	public function getNextBuildVersion()
	{
		$majorVersion = $this->getMajorVersion();
		$minorVersion = $this->getMinorVersion();
		$maintenenceVersion = $this->getMaintenenceVersion();
		$buildVersion = $this->getBuildVersion();
		$buildVersion++;
		$versionNumber = $this->_getFullVersionNumber(
						 	 $majorVersion, 
						     $minorVersion,
						     $maintenenceVersion,
						     $buildVersion
						 );
		return new Version($versionNumber);
	}

	public function getVersionNumber()
	{
		$versionNumber = $this->_getFullVersionNumber(
						 	 $this->getMajorVersion(),
						     $this->getMinorVersion(),
						     $this->getMaintenenceVersion(),
						     $this->getBuildVersion(),
						     $this->getStateVersion(),
						     $this->getSubstateVersion()
						 );
		return $versionNumber;
	}

	protected function _getFullVersionNumber(
		$majorVersion = null,
		$minorVersion = null,
		$maintenenceVersion = null,
		$bulidVersion = null
	) {
		$versionNumber = '';
		if(!is_null($majorVersion)) {
			$versionNumber .= $majorVersion;
		}
		if(!is_null($minorVersion)) {
			$versionNumber .= '.' . $minorVersion;
		}
		if(!is_null($maintenenceVersion)) {
			$versionNumber .= '.' . $maintenenceVersion;
		}
		if(!is_null($bulidVersion)) {
			$versionNumber .= '.' . $bulidVersion;
		}
		return $versionNumber;
	}

	protected function _getMatches($version) {
		$matches = array();
		$pattern = $this->_getVersionPattern();
		preg_match('/' . $pattern .'/i', $version, $matches);
		return $matches;
	}

	protected function _parseCompleteVersionNumber($version)
	{
		$matches = $this->_getMatches($version);
		foreach($this->version as $index => $value) {
			$matchValue = (isset($matches[$index+1]) ? $matches[$index+1] : 0);
			if("" == $matchValue) {
				$matchValue = 0;
			}
			$this->version[$index] = $matchValue;
		}
		$this->version[self::STATE] 	= $this->_getStateCode($this->version[self::STATE]);
	}

	protected function _parseState($statecode)
	{
		$stateCount = count($this->_getStates());
		$maxInt = bindec(str_repeat('1', self::BITS));
		return round(($maxInt / $stateCount) * $statecode); 
	}

	protected function _getStates()
	{
		return array(
			0 => 'development|dev',
			1 => 'alpha(\ ?\d+)?',
			2 => 'beta(\ ?\d+)?',
			2 => '(release candidate|rc)(\ ?\d+)?',
			3 => '(final|stable|[a-z^u]|0)',
			4 => '(update|u)(\ ?\d+)?',
			5 => '(sp|service\ ?pack)(\ ?\d+)?',
		);
	}

	protected function _getStateCode($state)
	{
		$returnState = 0;
		foreach ($this->_getStates() as $stateIndex => $pattern) {
			$matched = preg_match('/^' . $pattern . '$/i', $state);
			if($matched) {
				$returnState = $stateIndex;
				break;
			}
		}
		return $this->_parseState($returnState);
	}

	protected function _isVersionState($needle, $state)
	{
		$states = $this->_getStates(); 
		return preg_match('^' . $needle . '$', $states[$state]);
	}

	protected function _isState($state)
	{
		$pattern = '/^' . implode('|', $this->_getStates()) . '$/i';
		return preg_match($pattern, $state);
	}

	public function getInteger()
	{
		$seperator = '_';

		$return = '';
		$return .= $this->asBin($this->version[self::MAJOR])
		        .  $this->asBin($this->version[self::MINOR])
		        .  $this->asBin($this->version[self::MAINTENENCE])
		        .  $this->asBin($this->version[self::BUILD])
		        .  $this->asBin($this->version[self::STATE])
		        .  $this->asBin($this->version[self::SUBSTATEVERSION])
		        ;
        $return = bindec($return);
		return $return;
	}

	protected function asBin($integer)
	{
		$binString = (string) decbin($integer);
		$binString = str_pad($binString, self::BITS, 0, STR_PAD_LEFT);
		return $binString;
	}
}