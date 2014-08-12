<?php  namespace Nqxcode\LuceneSearch\Console;

use Illuminate\Console\Command;
use Nqxcode\LuceneSearch\Search;

use \App;

class RebuildCommand extends Command
{
    protected $name = 'search:rebuild';
    protected $description = 'Rebuild the search index';

    public function __construct()
    {
        parent::__construct();
    }

    public function fire()
    {
        $this->call('search:clear');

        /** @var Search $search */
        $search = \App::make('search');

        $modelRepositories = $search->config()->modelRepositories();

        if (count($modelRepositories)) {
            foreach ($modelRepositories as $modelRepository) {
                if (method_exists($modelRepository, 'allSearchable')) {
                    $all = $modelRepository->allSearchable();
                } else {
                    $all = $modelRepository->all();
                }
                foreach ($all as $model) {
                    $search->update($model);
                }
            }
            $this->info('Search index updated for all models!');
        } else {
            $this->error('No models found in config.php file..');
        }
    }
}