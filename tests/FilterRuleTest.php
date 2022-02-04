<?php

namespace HeadlessLaravel\Finders\Tests;

use HeadlessLaravel\Finders\Filter;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FilterRuleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_rule_appending()
    {
        $filter = Filter::make('append-rules')
            ->options(['1', '2', '3'])
            ->withRules('gt:2');

        $rules = $filter->getRules();

        $this->assertEquals(
            ['nullable', 'in:1,2,3', 'gt:2'],
            $rules['append-rules']
        );
    }

    public function test_rule_appending_with_multiple()
    {
        $filter = Filter::make('amount')
            ->options(['1', '2', '3'])
            ->withRules('gt:2')
            ->multiple();

        $filter->setRequest(request()->merge(['amount' => [1, 2, 3]]));

        $rules = $filter->getRules();

        $this->assertEquals(
            ['nullable', 'in:1,2,3', 'gt:2'],
            $rules['amount.*']
        );
    }

    public function test_rules_appending_with_multiple_but_single_value()
    {
        $filter = Filter::make('amount')
            ->options(['1', '2', '3'])
            ->withRules('gt:2')
            ->multiple();

        $filter->setRequest(request()->merge(['amount' => 1]));

        $rules = $filter->getRules();

        $this->assertEquals(
            ['nullable', 'in:1,2,3', 'gt:2'],
            $rules['amount']
        );
    }

    public function test_rules_appending_with_public_key()
    {
        $filter = Filter::make('other-name', 'append-rules')
            ->options(['1', '2', '3'])
            ->withRules('gt:2');

        $rules = $filter->getRules();

        $this->assertFalse(
            isset($rules['append-rules'])
        );

        $this->assertEquals(
            ['nullable', 'in:1,2,3', 'gt:2'],
            $rules['other-name']
        );
    }
}
