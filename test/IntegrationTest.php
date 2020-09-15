<?php
namespace Moxio\CommonMark\Extension\DefinitionList\Test;

use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use Moxio\CommonMark\Extension\DefinitionList\DefinitionListExtension;
use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase
{
    // Example based on https://michelf.ca/projects/php-markdown/extra/#def-list
    public function testParsesAndRendersSimpleDefinitionList(): void
    {
        $markdown = <<<MD
Apple
: Pomaceous fruit of plants of the genus Malus in the family Rosaceae.

Orange
: The fruit of an evergreen tree of the genus Citrus.
MD;
        $expectedHtml = <<<HTML
<dl>
  <dt>Apple</dt>
  <dd>Pomaceous fruit of plants of the genus Malus in the family Rosaceae.</dd>
  <dt>Orange</dt>
  <dd>The fruit of an evergreen tree of the genus Citrus.</dd>
</dl>
HTML;

        $this->assertMarkdownIsConvertedTo($expectedHtml, $markdown);
    }

    // Example based on https://michelf.ca/projects/php-markdown/extra/#def-list
    public function testSupportsArbitraryIndentsAfterTheColon(): void
    {
        $markdown = <<<MD
Apple
:   Pomaceous fruit of plants of the genus Malus in the family Rosaceae.

Orange
:   The fruit of an evergreen tree of the genus Citrus.
MD;
        $expectedHtml = <<<HTML
<dl>
  <dt>Apple</dt>
  <dd>Pomaceous fruit of plants of the genus Malus in the family Rosaceae.</dd>
  <dt>Orange</dt>
  <dd>The fruit of an evergreen tree of the genus Citrus.</dd>
</dl>
HTML;

        $this->assertMarkdownIsConvertedTo($expectedHtml, $markdown);
    }

    // Example from https://michelf.ca/projects/php-markdown/extra/#def-list
    public function testSupportsMultilineDefinitionsWithIndentation(): void
    {
        $markdown = <<<MD
Apple
:   Pomaceous fruit of plants of the genus Malus in
    the family Rosaceae.

Orange
:   The fruit of an evergreen tree of the genus Citrus.
MD;
        $expectedHtml = <<<HTML
<dl>
  <dt>Apple</dt>
  <dd>Pomaceous fruit of plants of the genus Malus in
the family Rosaceae.</dd>
  <dt>Orange</dt>
  <dd>The fruit of an evergreen tree of the genus Citrus.</dd>
</dl>
HTML;

        $this->assertMarkdownIsConvertedTo($expectedHtml, $markdown);
    }

    // Example from https://michelf.ca/projects/php-markdown/extra/#def-list
    public function testSupportsMultilineDefinitionsWithoutIndentation(): void
    {
        $markdown = <<<MD
Apple
:   Pomaceous fruit of plants of the genus Malus in
the family Rosaceae.

Orange
:   The fruit of an evergreen tree of the genus Citrus.
MD;
        $expectedHtml = <<<HTML
<dl>
  <dt>Apple</dt>
  <dd>Pomaceous fruit of plants of the genus Malus in
the family Rosaceae.</dd>
  <dt>Orange</dt>
  <dd>The fruit of an evergreen tree of the genus Citrus.</dd>
</dl>
HTML;

        $this->assertMarkdownIsConvertedTo($expectedHtml, $markdown);
    }

    public function testSupportsInlineMarkupInTermAndDefinition(): void
    {
        $markdown = <<<MD
`strpos`
:   Find the position of the first occurrence of a _substring_ in a string
MD;
        $expectedHtml = <<<HTML
<dl>
  <dt><code>strpos</code></dt>
  <dd>Find the position of the first occurrence of a <em>substring</em> in a string</dd>
</dl>
HTML;

        $this->assertMarkdownIsConvertedTo($expectedHtml, $markdown);
    }

    public function testSupportsLooseDefinitions(): void
    {
        $markdown = <<<MD
Apple

:   Pomaceous fruit of plants of the genus Malus in
the family Rosaceae.

Orange

:   The fruit of an evergreen tree of the genus Citrus.
MD;
        $expectedHtml = <<<HTML
<dl>
  <dt>Apple</dt>
  <dd>
    <p>Pomaceous fruit of plants of the genus Malus in
the family Rosaceae.</p>
  </dd>
  <dt>Orange</dt>
  <dd>
    <p>The fruit of an evergreen tree of the genus Citrus.</p>
  </dd>
</dl>
HTML;

        $this->assertMarkdownIsConvertedTo($expectedHtml, $markdown);
    }

    // Example adapted from https://michelf.ca/projects/php-markdown/extra/#def-list
    public function testSupportsMultipleParagraphsInDefinitions(): void
    {
        $markdown = <<<MD
Term 1

:   This is a definition with two paragraphs. Lorem ipsum
    dolor sit amet, consectetuer adipiscing elit. Aliquam
    hendrerit mi posuere lectus.

    Vestibulum enim wisi, viverra nec, fringilla in, laoreet
    vitae, risus.
MD;
        $expectedHtml = <<<HTML
<dl>
  <dt>Term 1</dt>
  <dd>
    <p>This is a definition with two paragraphs. Lorem ipsum
dolor sit amet, consectetuer adipiscing elit. Aliquam
hendrerit mi posuere lectus.</p>
    <p>Vestibulum enim wisi, viverra nec, fringilla in, laoreet
vitae, risus.</p>
  </dd>
</dl>
HTML;

        $this->assertMarkdownIsConvertedTo($expectedHtml, $markdown);
    }

    // Example from https://michelf.ca/projects/php-markdown/extra/#def-list
    public function testSupportsMultipleDefinitionsForOneTerm(): void
    {
        $markdown = <<<MD
Apple
:   Pomaceous fruit of plants of the genus Malus in
    the family Rosaceae.
:   An American computer company.

Orange
:   The fruit of an evergreen tree of the genus Citrus.
MD;
        $expectedHtml = <<<HTML
<dl>
  <dt>Apple</dt>
  <dd>Pomaceous fruit of plants of the genus Malus in
the family Rosaceae.</dd>
  <dd>An American computer company.</dd>
  <dt>Orange</dt>
  <dd>The fruit of an evergreen tree of the genus Citrus.</dd>
</dl>
HTML;

        $this->assertMarkdownIsConvertedTo($expectedHtml, $markdown);
    }

    // Example from https://michelf.ca/projects/php-markdown/extra/#def-list
    public function testSupportsMultipleTermsForOneDefinition(): void
    {
        $markdown = <<<MD
Term 1
Term 2
:   Definition a

Term 3
:   Definition b
MD;
        $expectedHtml = <<<HTML
<dl>
  <dt>Term 1</dt>
  <dt>Term 2</dt>
  <dd>Definition a</dd>
  <dt>Term 3</dt>
  <dd>Definition b</dd>
</dl>
HTML;

        $this->assertMarkdownIsConvertedTo($expectedHtml, $markdown);
    }

    public function testSupportsParagraphsBeforeTheDefinitionList(): void
    {
        $markdown = <<<MD
Introductory paragraph.

Apple
: Pomaceous fruit of plants of the genus Malus in the family Rosaceae.

Orange
: The fruit of an evergreen tree of the genus Citrus.
MD;
        $expectedHtml = <<<HTML
<p>Introductory paragraph.</p>
<dl>
  <dt>Apple</dt>
  <dd>Pomaceous fruit of plants of the genus Malus in the family Rosaceae.</dd>
  <dt>Orange</dt>
  <dd>The fruit of an evergreen tree of the genus Citrus.</dd>
</dl>
HTML;

        $this->assertMarkdownIsConvertedTo($expectedHtml, $markdown);
    }

    public function testSupportsParagraphsAfterTheDefinitionList(): void
    {
        $markdown = <<<MD
Apple
: Pomaceous fruit of plants of the genus Malus in the family Rosaceae.

Orange
: The fruit of an evergreen tree of the genus Citrus.

Concluding paragraph.
MD;
        $expectedHtml = <<<HTML
<dl>
  <dt>Apple</dt>
  <dd>Pomaceous fruit of plants of the genus Malus in the family Rosaceae.</dd>
  <dt>Orange</dt>
  <dd>The fruit of an evergreen tree of the genus Citrus.</dd>
</dl>
<p>Concluding paragraph.</p>
HTML;

        $this->assertMarkdownIsConvertedTo($expectedHtml, $markdown);
    }

    public function testSupportsMultilineParagraphsAfterTheDefinitionList(): void
    {
        $markdown = <<<MD
Apple
: Pomaceous fruit of plants of the genus Malus in the family Rosaceae.

Orange
: The fruit of an evergreen tree of the genus Citrus.

Concluding paragraph
split over multiple lines.
MD;
        $expectedHtml = <<<HTML
<dl>
  <dt>Apple</dt>
  <dd>Pomaceous fruit of plants of the genus Malus in the family Rosaceae.</dd>
  <dt>Orange</dt>
  <dd>The fruit of an evergreen tree of the genus Citrus.</dd>
</dl>
<p>Concluding paragraph
split over multiple lines.</p>
HTML;

        $this->assertMarkdownIsConvertedTo($expectedHtml, $markdown);
    }


    public function testSupportsMultipleParagraphsAfterTheDefinitionList(): void
    {
        $markdown = <<<MD
Apple
: Pomaceous fruit of plants of the genus Malus in the family Rosaceae.

Orange
: The fruit of an evergreen tree of the genus Citrus.

Concluding paragraph
split over multiple lines.

Another concluding paragraph.
MD;
        $expectedHtml = <<<HTML
<dl>
  <dt>Apple</dt>
  <dd>Pomaceous fruit of plants of the genus Malus in the family Rosaceae.</dd>
  <dt>Orange</dt>
  <dd>The fruit of an evergreen tree of the genus Citrus.</dd>
</dl>
<p>Concluding paragraph
split over multiple lines.</p>
<p>Another concluding paragraph.</p>
HTML;

        $this->assertMarkdownIsConvertedTo($expectedHtml, $markdown);
    }

    public function testSupportsOtherBlockAfterDefinitionList(): void
    {
        $markdown = <<<MD
Apple
: Pomaceous fruit of plants of the genus Malus in the family Rosaceae.

Orange
: The fruit of an evergreen tree of the genus Citrus.

> Quote about apples and oranges
MD;
        $expectedHtml = <<<HTML
<dl>
  <dt>Apple</dt>
  <dd>Pomaceous fruit of plants of the genus Malus in the family Rosaceae.</dd>
  <dt>Orange</dt>
  <dd>The fruit of an evergreen tree of the genus Citrus.</dd>
</dl>
<blockquote>
  <p>Quote about apples and oranges</p>
</blockquote>
HTML;

        $this->assertMarkdownIsConvertedTo($expectedHtml, $markdown);
    }

    public function assertMarkdownIsConvertedTo($expectedHtml, $markdown): void
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new DefinitionListExtension());

        $parser = new DocParser($environment);
        $renderer = new HtmlRenderer($environment);
        $actualOutput = $renderer->renderBlock($parser->parse($markdown));

        $this->assertXmlStringEqualsXmlString("<html>$expectedHtml</html>", "<html>$actualOutput</html>");
    }
}
