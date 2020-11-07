<?php declare(strict_types=1);
/**
 * This file is part of the Parsica library.
 *
 * Copyright (c) 2020 Mathias Verraes <mathias@verraes.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Verraes\Parsica\Expression;

use Verraes\Parsica\Parser;
use function Verraes\Parsica\Internal\FP\foldl;

/**
 * Build an expression parser from a term parser and an expression table.
 *
 * @api
 *
 * @template TTerm
 * @template TExpressionAST
 *
 * @psalm-param Parser<TTerm> $term
 * @psalm-param ExpressionType[] $expressionTable
 *
 * @psalm-return Parser<TExpressionAST>
 */
function expression(Parser $term, array $expressionTable): Parser
{
    /** @psalm-var Parser<TExpressionAST> $parser */
    $parser = foldl(
        $expressionTable,
        fn(Parser $previous, ExpressionType $next) => $next->buildPrecedenceLevel($previous),
        $term
    );
    return $parser;
}

/*
 * An binary operator in an expression. The operands of the expression will be passed into $transform to produce the
 * output of the expression parser.
 *
 * @api
 *
 * @template TSymbol
 * @template TExpressionAST
 * @psalm-param Parser<TSymbol> $symbol
 * @psalm-param callable(TExpressionAST, TExpressionAST):TExpressionAST $transform
 * @psalm-param string $label
 *
 * @return BinaryOperator<TSymbol, TExpressionAST>
 */
function binaryOperator(Parser $symbol, callable $transform, string $label = ""): BinaryOperator
{
    return new BinaryOperator($symbol, $transform, $label);
}

/*
 * An unary operator in an expression. The operands of the expression will be passed into $transform to produce the
 * output of the expression parser.
 *
 * @api
 *
 * @template TSymbol
 * @template TExpressionAST
 * @psalm-param Parser<TSymbol> $symbol
 * @psalm-param callable(TExpressionAST):TExpressionAST $transform
 * @psalm-param string $label
 *
 * @return UnaryOperator<TSymbol, TExpressionAST>
 */
function unaryOperator(Parser $symbol, callable $transform, string $label = ""): UnaryOperator
{
    return new UnaryOperator($symbol, $transform, $label);
}

/*
 * @api
 * @psalm-param non-empty-list<BinaryOperator<TSymbol, TExpressionAST>>
 * @psalm-return LeftAssoc<TSymbol, TExpressionAST>
 */
function leftAssoc(BinaryOperator ...$operators): LeftAssoc
{
    return new LeftAssoc($operators);
}

/*
 * @api
 *
 * @psalm-param non-empty-list<BinaryOperator<TSymbol,TExpressionAST>>
 * @psalm-return RightAssoc<TSymbol, TExpressionAST>
 */
function rightAssoc(BinaryOperator ...$operators): RightAssoc
{
    return new RightAssoc($operators);
}

/*
 * @api
 *
 * @psalm-param BinaryOperator<TSymbol, TExpressionAST>
 * @psalm-return NonAssoc<TSymbol, TExpressionAST>
 */
function nonAssoc(BinaryOperator $operator): NonAssoc
{
    return new NonAssoc($operator);
}

/*
 * @api
 *
 *
 * @psalm-param non-empty-list<BinaryOperator<TSymbol, TExpressionAST>>
 * @psalm-return Prefix<TExpressionAST>
 */
function prefix(UnaryOperator ...$operators): Prefix
{
    return new Prefix($operators);
}

/*
 * @api
 *
 * @psalm-param non-empty-list<BinaryOperator<TSymbol, TExpressionAST>>
 * @psalm-return PostFix<TExpressionAST>
 */
function postfix(UnaryOperator ...$operators): Postfix
{
    return new Postfix($operators);
}
