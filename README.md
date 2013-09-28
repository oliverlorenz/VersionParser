VersionParser
=============

Simple class for calculating und comparing version numbers.

Usage
-----

	$version1 = new Version('1.0');
	$version2 = new Version('1.0.0');
	$version3 = new Version('2.0');

Compare with Versions
---------------------

	$version1->equals($version2);
	// true

	$newVersion = version1->getNextMajorVersion();
	echo $newVersion->getVersionNumber();
	// 2.0