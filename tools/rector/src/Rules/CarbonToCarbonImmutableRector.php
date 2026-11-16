<?php

declare(strict_types=1);

namespace Tools\Rector\Rules;

use Carbon\CarbonImmutable;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use Rector\Rector\AbstractRector;
use Rector\RuleDoc\CodeSample\CodeSample;
use Rector\RuleDoc\RuleDefinition;

final class CarbonToCarbonImmutableRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert Carbon, Date, and DateTime usages to CarbonImmutable.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

Carbon::now();
Date::parse('2024-01-01');
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Carbon\CarbonImmutable;

CarbonImmutable::now();
CarbonImmutable::parse('2024-01-01');
CODE_SAMPLE
                ),
            ],
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [New_::class, StaticCall::class];
    }

    /**
     * @param  New_|StaticCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof New_) {
            if (! $node->class instanceof Name || ! $this->isConvertibleDateClass($node->class)) {
                return null;
            }

            $node->class = new FullyQualified(CarbonImmutable::class);

            return $node;
        }

        if (! $node->class instanceof Name || ! $node->name instanceof Identifier) {
            return null;
        }

        if (! $this->isConvertibleDateClass($node->class) && ! $this->isName($node->class, 'Illuminate\Support\Facades\Date')) {
            return null;
        }

        if (! method_exists(CarbonImmutable::class, $node->name->toString())) {
            return null;
        }

        return new StaticCall(
            new FullyQualified(CarbonImmutable::class),
            $node->name,
            $node->args,
            $node->getAttributes(),
        );
    }

    private function isConvertibleDateClass(Name $name): bool
    {
        return $this->isName($name, 'Carbon\Carbon')
            || $this->isName($name, 'Illuminate\Support\Carbon')
            || $this->isName($name, 'DateTime')
            || $this->isName($name, 'DateTimeImmutable');
    }
}
