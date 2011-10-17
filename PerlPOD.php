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
	'version'	 => '0.1',
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
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
		  );

	// Round 1: Get the POD. We use perldoc to extract the POD because...it's easier that way :)
	$ph = proc_open("perldoc -u $module", $d, $p);
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

	// Round 2: Turn the POD into Mediawiki markup
	// We use pod2wiki (part of Pod::Simple::Wiki) because...it's easier that way :)
	$ph = proc_open('pod2wiki --style mediawiki -', $d, $p);

	// POD goes in
	fwrite( $p[0], $pod );
	fclose( $p[0] );

	// Wiki markup comes out
	$output = stream_get_contents($p[1]);
	fclose($p[1]);

	// As we fed input and caught output, ASSume the only thing that can go wrong is pod2wiki not being installed
	// Yeah...BRB, feeding my unicorn...
	if ( proc_close($ph) ) {
		return "{error running pod2wiki - please ensure it's installed properly}";
	}

	// Return the wiki markup
	return $output;
}

?>
