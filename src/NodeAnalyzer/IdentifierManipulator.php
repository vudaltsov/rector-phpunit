<?php

declare(strict_types=1);

namespace Rector\PHPUnit\NodeAnalyzer;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\NodeNameResolver\NodeNameResolver;

/**
 * This class renames node identifier, e.g. ClassMethod rename:
 *
 * -public function someMethod()
 * +public function newMethod()
 */
final class IdentifierManipulator
{
    public function __construct(
        private readonly NodeNameResolver $nodeNameResolver
    ) {
    }

    /**
     * @param string[] $renameMethodMap
     */
    public function renameNodeWithMap(
        ClassConstFetch | MethodCall | PropertyFetch | StaticCall | ClassMethod $node,
        array $renameMethodMap
    ): void {
        $oldNodeMethodName = $this->resolveOldMethodName($node);
        if ($oldNodeMethodName === null) {
            return;
        }

        $node->name = new Identifier($renameMethodMap[$oldNodeMethodName]);
    }

    private function resolveOldMethodName(
        ClassConstFetch | MethodCall | PropertyFetch | StaticCall | ClassMethod $node
    ): ?string {
        if ($node instanceof StaticCall || $node instanceof MethodCall) {
            return $this->nodeNameResolver->getName($node->name);
        }

        return $this->nodeNameResolver->getName($node);
    }
}
