<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class AnnotationHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	/** @var \PHP_CodeSniffer\Files\File */
	private $testedCodeSnifferFile;

	public function testClassWithAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithAnnotation'), '@see');
		self::assertCount(1, $annotations);
		self::assertSame('@see', $annotations[0]->getName());
		self::assertSame(4, $this->getLineByPointer($annotations[0]->getPointer()));
		self::assertSame('https://www.slevomat.cz', $annotations[0]->getContent());
	}

	public function testClassWithoutAnnotation(): void
	{
		self::assertCount(0, AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithoutAnnotation'), '@see'));
	}

	public function testConstantWithAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_ANNOTATION'), '@var');
		self::assertCount(1, $annotations);
		self::assertSame('@var', $annotations[0]->getName());
		self::assertSame(10, $this->getLineByPointer($annotations[0]->getPointer()));
		self::assertSame('bool', $annotations[0]->getContent());
	}

	public function testConstantWithoutAnnotation(): void
	{
		self::assertCount(0, AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITHOUT_ANNOTATION'), '@var'));
	}

	public function testPropertyWithAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withAnnotation'), '@var');
		self::assertCount(1, $annotations);
		self::assertSame('@var', $annotations[0]->getName());
		self::assertSame(17, $this->getLineByPointer($annotations[0]->getPointer()));
		self::assertSame('int', $annotations[0]->getContent());
	}

	public function testPropertyWithoutAnnotation(): void
	{
		self::assertCount(0, AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withoutAnnotation'), '@var'));
	}

	public function testFunctionWithAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withAnnotation'), '@param');
		self::assertCount(2, $annotations);
		self::assertSame('@param', $annotations[0]->getName());
		self::assertSame(29, $this->getLineByPointer($annotations[0]->getPointer()));
		self::assertSame('string $a', $annotations[0]->getContent());
		self::assertSame('@param', $annotations[1]->getName());
		self::assertSame(30, $this->getLineByPointer($annotations[1]->getPointer()));
		self::assertSame('string $b', $annotations[1]->getContent());
	}

	public function testFunctionWithParametrizedAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withParametrizedAnnotation'), '@Route');
		self::assertCount(1, $annotations);
		self::assertSame('"/", name="homepage"', $annotations[0]->getParameters());
		self::assertNull($annotations[0]->getContent());
	}

	public function testFunctionWithParametrizedAnnotationContainingParenthesis(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withParametrizedAnnotationContainingParenthesis'), '@Security');
		self::assertCount(1, $annotations);
		self::assertSame('"is_granted(\'ROLE_ADMIN\')"', $annotations[0]->getParameters());
		self::assertNull($annotations[0]->getContent());
	}

	public function testFunctionWithMultilineParametrizedAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withMultilineParametrizedAnnotation'), '@Route');
		self::assertCount(1, $annotations);
		self::assertSame("\"/configs/{config}/domains/{domain}/locales/{locale}/messages\", name=\"jms_translation_update_message\",\ndefaults = {\"id\" = null}, options = {\"i18n\" = false}, methods={\"PUT\"}", $annotations[0]->getParameters());
		self::assertNull($annotations[0]->getContent());
	}

	public function testFunctionWithParametrizedAnnotationWithoutParameters(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withParametrizedAnnotationWithoutParameters'), '@Assert\Callback');
		self::assertCount(1, $annotations);
		self::assertNull($annotations[0]->getParameters());
		self::assertNull($annotations[0]->getContent());
	}

	public function testFunctionWithoutAnnotation(): void
	{
		self::assertCount(0, AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withoutAnnotation'), '@param'));
	}

	private function getTestedCodeSnifferFile(): \PHP_CodeSniffer\Files\File
	{
		if ($this->testedCodeSnifferFile === null) {
			$this->testedCodeSnifferFile = $this->getCodeSnifferFile(
				__DIR__ . '/data/annotation.php'
			);
		}
		return $this->testedCodeSnifferFile;
	}

	private function getLineByPointer(int $pointer): int
	{
		return $this->getTestedCodeSnifferFile()->getTokens()[$pointer]['line'];
	}

}
