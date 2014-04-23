<?hh //strict
namespace HackUnit\Loading;

use HackUnit\Core\TestCase;

require_once __DIR__ . '/../../../test/fixtures/loading/excluded/ThreeTest.php';

class ConventionalLoaderTest extends TestCase
{
    protected ?ConventionalLoader $loader;
    protected string $path = '';

    <<Override>> public function setUp(): void
    {
        $this->path = __DIR__ . '/../../../test/fixtures/loading';
        $this->loader = new ConventionalLoader($this->path);
    }

    public function test_getTestCasePaths_should_return_paths_to_test_cases(): void
    {
        if (! $this->loader) throw new \Exception("loader and path cannot be null");
        $paths = $this->loader->getTestCasePaths();
        $this->expect($paths->count())->toEqual(3);
        $this->expect($paths->at(0))->toEqual($this->path . '/OneTest.php');
        $this->expect($paths->at(1))->toEqual($this->path . '/TwoTest.php');
        $this->expect($paths->at(2))->toEqual($this->path . '/excluded/ThreeTest.php');
    }

    public function test_load_should_return_classes_ending_in_Test_for_every_method(): void
    {
        if (! $this->loader) throw new \Exception("loader cannot be null");
        $pattern = '/Test$/';
        $objects = $this->loader->load();
        $this->expect($objects->count())->toEqual(6);

        $oneTest = $objects->at(0);
        $oneTest2 = $objects->at(1);
        $this->expect($oneTest->getName())->toEqual('testOne');
        $this->expect($oneTest2->getName())->toEqual('testTwo');

        $twoTest = $objects->at(2);
        $twoTest2 = $objects->at(3);
        $this->expect($twoTest->getName())->toEqual('testThree');
        $this->expect($twoTest2->getName())->toEqual('testFour');

        $threeTest = $objects->at(4);
        $threeTest2 = $objects->at(5);
        $this->expect($threeTest->getName())->toEqual('testFive');
        $this->expect($threeTest2->getName())->toEqual('testSix');
    }
    
    public function test_loadSuite_should_use_results_of_load_to_create_a_TestSuite(): void
    {
        if (! $this->loader) throw new \Exception("loader cannot be null");
        $suite = $this->loader->loadSuite();
        $tests = $suite->getTests();
        $this->expect($tests->count())->toEqual(6);
    }

    public function test_getTestCasePaths_should_exclude_dirs(): void
    {
        $loader = new ConventionalLoader($this->path, Vector {$this->path . '/excluded'});
        $paths = $loader->getTestCasePaths();
        $this->expect($paths->count())->toEqual(2);
    }
}