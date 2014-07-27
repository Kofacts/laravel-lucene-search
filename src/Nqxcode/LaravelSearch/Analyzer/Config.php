<?php namespace Nqxcode\LaravelSearch\Analyzer;

use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use ZendSearch\Lucene\Analysis\Analyzer\Common\AbstractCommon;
use ZendSearch\Lucene\Analysis\TokenFilter\StopWords;
use ZendSearch\Lucene\Search\QueryParser;

use \App;

/**
 * Class Config
 * @package Nqxcode\LaravelSearch\Analyzer
 */
class Config
{
    private $filters;
    private $stopWordFiles;

    public function __construct(array $filerClasses, array $stopWordFiles)
    {
        QueryParser::setDefaultEncoding('utf-8'); // TODO remove it in other place

        $this->filters = array_map(function ($filerClass) {
            return App::make($filerClass);
        }, $filerClasses);

        foreach ($stopWordFiles as $stopWordFile) {
            if (!is_file($stopWordFile)) {
                throw new \InvalidArgumentException("File '{$stopWordFile}' with stop words doesn't exit.");
            }
        }

        $this->stopWordFiles = $stopWordFiles;
    }

    public function setDefaultAnalyzer()
    {
        /** @var AbstractCommon $analyzer */
        $analyzer = App::make('search.analyzer');

        foreach ($this->stopWordFiles as $stopWordFile) {
            $stopWordsFilter = new StopWords;
            $stopWordsFilter->loadFromFile($stopWordFile);
            $analyzer->addFilter($stopWordsFilter);
        }

        foreach ($this->filters as $filter) {
            $analyzer->addFilter($filter);
        }

        Analyzer::setDefault($analyzer);
    }

    public function setAnalyzerForHighlighter()
    {
        /** @var AbstractCommon $analyzer */
        $analyzer = \App::make('search.analyzer');

        foreach ($this->filters as $filter) {
            $analyzer->addFilter($filter);
        }

        Analyzer::setDefault($analyzer);
    }
}