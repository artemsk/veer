<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdministrationElementsPageTest extends TestCase
{

    /**
     *  + add
        + delete
        request
        + sort
        + toggleStatus
     *  attach
        detach
        + find
        freeForm
        + toggle
        + update
     *  + image
     *  + file
     *  + getEntity
     *  + getId
     * 
     */
    
    protected $page;
    protected $testImage;
    protected $testFile;

    public function setUp()
    {
        parent::setUp();
        $this->page = new \Veer\Services\Administration\Elements\Page;
        $admin = \Veer\Models\UserAdmin::where('banned', 0)->first();
        \Auth::loginUsingId($admin->users_id);

        $this->testImage = __DIR__ . '/../studs/image.jpg';
        $this->testFile = __DIR__ . '/../studs/file.txt';
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Missing argument 1
     */
    public function testAddEmpty()
    {
        $this->page->add();
    }

    public function testAddWithEmptyOrWrongData()
    {
        $this->page->add([]);
        $this->assertEmpty($this->page->getId());
        $this->page->add('String');
        $this->assertEmpty($this->page->getId());
    }

    protected function getTestFile($file)
    {
        $copyFileName = __DIR__ . '/../studs/tmp_file.' . pathinfo($file)['extension'];
        file_put_contents($copyFileName, file_get_contents($file));

        $fileUpload = new \Symfony\Component\HttpFoundation\File\UploadedFile(
            $copyFileName, pathinfo($file)['basename'], \File::mimeType($file), \File::size($file), 0, 1
        );

        return $fileUpload;
    }

    public function testAddData()
    {
        $image = $this->getTestFile($this->testImage);
        $file = $this->getTestFile($this->testFile);

        $this->page->add([
            'title' => 'Title',
            'url' => ' http://bolshaya.net ',
            'txt' => '{{ Small text }} Main Text',
            'uploadImage' => $image,
            'uploadFile' => $file
        ]);

        $entity = $this->page->getEntity();
        $this->assertTrue(is_object($entity));
        $this->assertGreaterThan(0, $entity->id);
        $this->assertEquals('Title', $entity->title);
        $this->assertEquals('http://bolshaya.net', $entity->url);
        $this->assertEquals('Small text', $entity->small_txt);
        $this->assertEquals('Main Text', $entity->txt);

        $img = $entity->images->first();
        $this->assertTrue(is_object($img));
        $this->assertGreaterThan(0, $img->id);
        $this->assertNotEmpty($img->img);

        list(, $assets_path, $folder, , ,) = $this->page->uploadDataProvider['image'];
        $path = (!config('veer.use_cloud_image')) ?  base_path() . '/' . $folder . '/' .
                config('veer.' . $assets_path) : config('veer.' . $assets_path);
        $this->assertTrue(file_exists($path . '/' . $img->img));

        $f = $entity->downloads->first();
        $this->assertTrue(is_object($f));
        $this->assertGreaterThan(0, $f->id);
        $this->assertNotEmpty($f->fname);

        list(, $assets_path, $folder, , ,) = $this->page->uploadDataProvider['file'];
        $path = (!config('veer.use_cloud_image')) ?  storage_path() . '/' . $folder . '/' .
                config('veer.' . $assets_path) : config('veer.' . $assets_path);
        $this->assertTrue(file_exists($path . '/' . $f->fname));
    }

    public function testRepeatAdd()
    {
        $this->testAddData();
    }

    public function testToggleStatus()
    {
        $entity = \Veer\Models\Page::first();
        $this->assertTrue(is_object($entity));

        $status = $entity->hidden;
        $this->page->toggleStatus(0);
        $this->page->toggleStatus($entity->id);
        $this->page->find($entity->id);
        $updatedEntity = $this->page->getEntity();
        $this->assertTrue(is_object($updatedEntity));
        
        $this->assertNotEquals($updatedEntity->hidden, $status);
    }

    public function testSort()
    {
        // TODO: sorting now are limited to paginated elements, paginate is hard coded to 24 items for now
        $all = \Veer\Models\Page::take(24)->get()->sortBy('manual_order');
        $first = $all->first();
        $last = $all->last();

        $this->page->sort([]);
        $this->page->sort('String');
        $this->page->sort([
            'oldindex' => 23,
            'newindex' => 0,
            'parentid' => '',
            '_refurl' => '?sort=manual_order&sort_direction=asc&page=1',
            'sort' => 'manual_order',
            'sort_direction' => 'asc'
        ]);

        $all = \Veer\Models\Page::take(24)->get()->sortBy('manual_order');
        $new_first = $all->first();

        $this->assertNotEquals($first->id, $new_first->id);
        $this->assertEquals($last->id, $new_first->id);
    }

    public function testDelete()
    {
        $entity = \Veer\Models\Page::first();
        $this->assertTrue(is_object($entity));

        $this->page->delete($entity->id);

        $entity = \Veer\Models\Page::where('id', $entity->id)->first();
        $this->assertTrue(!is_object($entity));
    }

    public function testToggle()
    {
        $entity = \Veer\Models\Page::first();
        $this->assertTrue(is_object($entity));

        $status = $entity->hidden;
        $this->page->find($entity->id)->toggle();
        $updatedEntity = $this->page->getEntity();
        $this->assertTrue(is_object($updatedEntity));

        $this->assertNotEquals($updatedEntity->hidden, $status);
    }

    public function testUpdate()
    {
        $entity = \Veer\Models\Page::first();
        $this->assertTrue(is_object($entity));

        $this->page->find($entity->id)->update([
            'show_small' => 1,
            'show_title' => 1,
            'url' => ' http:://bolshaya.net '
        ]);

        $this->assertEquals(1, $this->page->getEntity()->show_small);
        $this->assertEquals(1, $this->page->getEntity()->show_title);
        $this->assertEquals('http:://bolshaya.net', $this->page->getEntity()->url);

        $this->page->find($entity->id)->update([
            'show_small' => 0,
        ]);

        $this->assertEquals(0, $this->page->getEntity()->show_small);
    }

    public function testUploadImage()
    {
        $image = $this->getTestFile($this->testImage);

        $this->page->add([
            'title' => 'TestUploadImage',
            'url' => ' http://bolshaya.net ',
            'txt' => '{{ Small text }} Main Text',
        ])->image($image);

        $img = $this->page->getEntity()->images->first();
        $this->assertTrue(is_object($img));
        $this->assertGreaterThan(0, $img->id);
        $this->assertNotEmpty($img->img);

        list(, $assets_path, $folder, , ,) = $this->page->uploadDataProvider['image'];
        $path = (!config('veer.use_cloud_image')) ?  base_path() . '/' . $folder . '/' .
                config('veer.' . $assets_path) : config('veer.' . $assets_path);
        $this->assertTrue(file_exists($path . '/' . $img->img));
    }

    public function testUploadFile()
    {
        $file = $this->getTestFile($this->testFile);

        $this->page->add([
            'title' => 'TestUploadFile',
            'url' => ' http://bolshaya.net ',
            'txt' => '{{ Small text }} Main Text',
        ])->file($file);

        $f = $this->page->getEntity()->downloads->first();
        $this->assertTrue(is_object($f));
        $this->assertGreaterThan(0, $f->id);
        $this->assertNotEmpty($f->fname);

        list(, $assets_path, $folder, , ,) = $this->page->uploadDataProvider['file'];
        $path = (!config('veer.use_cloud_image')) ?  storage_path() . '/' . $folder . '/' .
                config('veer.' . $assets_path) : config('veer.' . $assets_path);
        $this->assertTrue(file_exists($path . '/' . $f->fname));
    }

    public function testAttachWrongData()
    {
        $this->page->add([
            'title' => 'TestAttach',
            'url' => ' http://bolshaya.net ',
            'txt' => '{{ Small text }} Main Text',
        ])->attach('images', 99999)
          ->attach('files', 99999)
                ->attach('categories', 99999)
                ->attach('attributes', 1211212)
                ->attach('tags', 12121121) // FIX
                ->attach('products', 121121131)
                ->attach('parent_pages', 1212121212)
                ->attach('child_pages', 12121212121);
    }
}
