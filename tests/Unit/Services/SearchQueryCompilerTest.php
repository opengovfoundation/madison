<?php

namespace Tests\Unit;

use App\Services\SearchQueryCompiler;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SearchQueryCompilerTest extends TestCase
{
    public function compileProvider()
    {
        return [
            ['test me', '+test* +me*'],
            ['test mé', '+test* +mé*'],
            ['test |me', '+test* me*'],
            ['test |"me"', '+test* "me"'],
            ['"test me"', '+"test me"'],
            ['some "test me" more', '+some* +"test me" +more*'],
            ['+test +me', '+test* +me*'],
            ['+apple ~macintosh', '+apple* ~macintosh*'],
            ['test -me', '+test* -me*'],
            ['apple (>turnover <strudel)', '+apple* +(>turnover <strudel)'],
            ['apple -(>turnover <strudel)', '+apple* -(>turnover <strudel)'],
            ['"word1 word2 word3" @8', '+"word1 word2 word3" @8'],
        ];
    }

    /**
     * @dataProvider compileProvider
     */
    public function testCompile($query, $expected)
    {
        $this->assertEquals($expected, SearchQueryCompiler::compile($query));
    }
}
