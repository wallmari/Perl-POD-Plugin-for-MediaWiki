<?php
if (!defined('MEDIAWIKI')) die();
/*
 * A magic word extension to embed Perl POD into a wiki page
 *
 * @addtogroup Extensions
 *
 * @author Richard Wallman <richard.wallman@bossolutions.co.uk>
 * @copyright Copyright © 2011, Richard Wallman
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
*/

$wgExtensionCredits['parserhook'][] = array(
	'name'           => 'PerlPOD',
	'author'         => 'Richard Wallman',
	'description'    => 'Embed Perl POD (formatted for MediaWiki) in a page',
	'descriptionmsg' => 'perlpod-desc',
	'version'	 => '0.2',
);

$wgHooks['ParserFirstCallInit'][] = 'PerlPOD_Setup';
$wgHooks['LanguageGetMagic'][] = 'PerlPOD_Magic';

function PerlPOD_Setup( &$parser ) {
	$parser->setFunctionHook( 'pod', 'PerlPOD_Render' );
	return true;
}
 
function PerlPOD_Magic( &$magicWords, $langCode ) {
	$magicWords['pod'] = array( 0, 'pod' );
	return true;
}
 
function PerlPOD_Render( $parser, $param = '' ) {
	// As we're passing it to a shell, make sure it's clean	
	$module = EscapeShellArg($param);

	// We'll use these for getting data into and out from the utilities
	$d = array(
			1 => array("pipe", "w")
		  );

	// Get the POD and render it directly into MediaWiki markup.
	// We use perldoc to extract the POD because...it's easier that way :)
	$ph = proc_open("perldoc -M Pod::Simple::Wiki::Mediawiki $module", $d, $p);
	$pod = stream_get_contents($p[1]);
	fclose($p[1]);
	$error = proc_close($ph);

	// Handle any errors when getting the POD
	switch ($error) {
		case 1:		// Module not found
			return "Perl module not found - check spelling, and ensure it's installed on this system";
			break;
		case 127:	// perldoc utility not found
			return "'perldoc' utility not found";
			break;
	};

	return $pod;
}

?>
