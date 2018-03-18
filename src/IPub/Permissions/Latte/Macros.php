<?php
/**
 * Macros.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Permissions!
 * @subpackage     Latte
 * @since          1.0.0
 *
 * @date           10.10.14
 */

declare(strict_types = 1);

namespace IPub\Permissions\Latte;

use Nette\Utils;

use Latte;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

/**
 * Permissions latte macros definition
 *
 * @package        iPublikuj:Permissions!
 * @subpackage     Latte
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class Macros extends MacroSet
{
	/**
	 * @param Compiler $compiler
	 * 
	 * @return void
	 */
	public static function install(Compiler $compiler) : void
	{
		$me = new static($compiler);

		/**
		 * {ifAllowed role => 'some role', resource => 'some resource'}...{/ifAllowed}
		 */
		$me->addMacro('ifAllowed', [$me, 'macroIfAllowed'], [$me, 'macroIfAllowedEnd']);

		/**
		 * <a n:allowedHref="Presenter:action">...</a>
		 */
		$me->addMacro('allowedHref', [$me, 'macroIfAllowedLink'], [$me, 'macroIfAllowedLinkEnd']);
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public static function macroIfAllowed(MacroNode $node, PhpWriter $writer) : string
	{
		return $writer->write('if($presenter->context->getByType("IPub\Permissions\Access\LatteChecker")->isAllowed(%node.array)){');
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public static function macroIfAllowedEnd(MacroNode $node, PhpWriter $writer) : string
	{
		return $writer->write('}');
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 *
	 * @throws Latte\CompileException
	 */
	public static function macroIfAllowedLink(MacroNode $node, PhpWriter $writer) : string
	{
		// This macro is allowed only as n:macro in <a ></a> element
		if (Utils\Strings::lower($node->htmlNode->name) !== 'a') {
			throw new Latte\CompileException("Macro n:allowedHref is allowed only in link element, you used it in {$node->htmlNode->name}.");
		}

		return $writer->write('if($presenter->context->getByType("IPub\Permissions\Access\LinkChecker")->isAllowed(%node.word)){');
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public static function macroIfAllowedLinkEnd(MacroNode $node, PhpWriter $writer) : string
	{
		return $writer->write('}');
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroHref(MacroNode $node, PhpWriter $writer) : string
	{
		return $writer->write(' ?> href="<?php echo %escape(' . ($node->name === 'plink' ? '$_presenter' : '$_control') . '->link(%node.word, %node.array?)) ?>"<?php ');
	}
}
