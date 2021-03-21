<?php
declare(strict_types=1);

namespace App\Rules;

use Mockery;
use Mockery\MockInterface;
use ReflectionClass;
use Tests\TestCase;

class VideoUnitTest extends TestCase
{

    public function testCategoriesIdFields()
    {
        $rule = new GenresHasCategoriesRule([1, 1, 2, 2]);
        $reflactionClass = new ReflectionClass(GenresHasCategoriesRule::class);
        $reflactionProperty = $reflactionClass->getProperty('categoriesId');
        $reflactionProperty->setAccessible(true);

        $categoriesId = $reflactionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1, 2], $categoriesId);
    }

    public function testGenresIdValue()
    {
        $rule = $this->createRuleMock([]);
        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturnNull();

        $rule->passes('', [1, 1, 2, 2]);
        $reflactionClass = new ReflectionClass(GenresHasCategoriesRule::class);
        $reflactionProperty = $reflactionClass->getProperty('genresId');
        $reflactionProperty->setAccessible(true);

        $genresId = $reflactionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1, 2], $genresId);
    }

    public function testPassesReturnsFalseWhenCategoriesOrGenresIsArrayEmpty()
    {
        $rule = $this->createRuleMock([1]);
        $this->assertFalse($rule->passes('', []));

        $rule = $this->createRuleMock([]);
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesReturnsFalseWhenGetRowsIsEmpty()
    {
        $rule = $this->createRuleMock([]);
        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect());
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesReturnsFalseWhenHasCategoriesWithoutGenres()
    {
        $rule = $this->createRuleMock([1, 2]);
        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect(['category_id' => 1]));
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesIsValid()
    {
        $rule = $this->createRuleMock([1, 2]);
        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2]
            ]));
        $this->assertTrue($rule->passes('', [1]));

        $rule = $this->createRuleMock([1, 2]);
        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2],
                ['category_id' => 1],
                ['category_id' => 2]
            ]));
        $this->assertTrue($rule->passes('', [1]));
    }

    protected function createRuleMock(array $categoriesId): MockInterface
    {
        return Mockery::mock(GenresHasCategoriesRule::class, [$categoriesId])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

}
