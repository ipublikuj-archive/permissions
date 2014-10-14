<?php
/**
 * Macros.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Permissions!
 * @subpackage	Latte
 * @since		5.0
 *
 * @date		10.10.14
 */

namespace IPub\Permissions\Latte;

use Nette;
use Nette\Utils;

use Latte\Compiler;
use Latte\CompileException;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

class Macros extends MacroSet
{
	/**
	 * @param Compiler $compiler
	 */
	public static function install(Compiler $compiler)
	{
		$me = new static($compiler);

		/**
		 * {ifAllowed role => 'some role', resource => 'some resource'}...{/ifAllowed}
		 */
		$me->addMacro('ifAllowed', array($me, 'macroIfAllowed'), array($me, 'macroIfAllowedEnd'));

		/**
		 * <a n:allowedHref="Presenter:action">...</a>
		 */
		$me->addMacro('allowedHref', array($me, 'macroIfAllowedLink'), array($me, 'macroIfAllowedLinkEnd'));
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public static function macroIfAllowed(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write('if($presenter->context->getByType("IPub\Permissions\Access\LatteChecker")->isAllowed(%node.array)){');
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public static function macroIfAllowedEnd(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write('}');
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 *
	 * @throws CompileException
	 */
	public static function macroIfAllowedLink(MacroNode $node, PhpWriter $writer)
	{
		// This macro is allowed only as n:macro in <a ></a> element
		if (Utils\Strings::lower($node->htmlNode->name) != 'a') {
			throw new CompileException("Macro n:allowedHref is allowed only in link element, you used it in {$node->htmlNode->name}.");
		}

		return $writer->write('if($presenter->context->getByType("IPub\Permissions\Access\LinkChecker")->isAllowed(%node.word)){');
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public static function macroIfAllowedLinkEnd(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write('}');
	}

	public function macroHref(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write(' ?> href="<?php echo %escape(' . ($node->name === 'plink' ? '$_presenter' : '$_control') . '->link(%node.word, %node.array?)) ?>"<?php ');
	}
}