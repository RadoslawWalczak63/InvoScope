<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class ScraperTest extends DuskTestCase
{
    /**
     * @throws Throwable
     */
    public function test_selenium_as_scraper()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://books.toscrape.com/')
                ->waitFor('.product_pod')
                ->pause(2000)
                ->assertSee('All products');

            $extractionScript = "
                return Array.from(document.querySelectorAll('.product_pod')).map(pod => {
                    return {
                        title: pod.querySelector('h3 > a').getAttribute('title'),
                        price: pod.querySelector('.price_color').innerText,
                        rating: pod.querySelector('.star-rating').className.split(' ').pop(),
                        link: pod.querySelector('h3 > a').href,
                        in_stock: pod.querySelector('.instock.availability').innerText.trim().includes('In stock')
                    };
                });
            ";

            $pageOneData = collect($browser->script($extractionScript)[0]);

            $browser->scrollIntoView('.pager')
                ->pause(1500)
                ->clickLink('next')
                ->waitForLocation('/catalogue/page-2.html')
                ->waitFor('.product_pod')
                ->pause(2000);

            $pageTwoData = collect($browser->script($extractionScript)[0]);

            $allBooks = $pageOneData->merge($pageTwoData);

            dump($allBooks->take(3)->toArray());

            $this->assertCount(40, $allBooks);

            $allBooks->each(function ($book) {
                $this->assertArrayHasKey('title', $book);
                $this->assertArrayHasKey('rating', $book);
                $this->assertIsBool($book['in_stock']);
                $this->assertTrue($book['in_stock']);
                $this->assertStringStartsWith('£', $book['price']);
            });

            $expensiveBooks = $allBooks->filter(function ($book) {
                $price = (float) str_replace('£', '', $book['price']);

                return $price > 50.00;
            });

            $this->assertGreaterThan(0, $expensiveBooks->count());
        });
    }

    /**
     * @throws Throwable
     */
    public function test_navigate_categories_and_verify()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://books.toscrape.com/')
                ->waitFor('.side_categories')
                ->pause(1000);

            $categoryName = $browser->text('.side_categories ul li ul li:nth-child(3) a');

            $browser->click('.side_categories ul li ul li:nth-child(3) a')
                ->pause(2000)
                ->assertSeeIn('h1', $categoryName)
                ->assertPathContains('catalogue/category/books');

            $booksInCategory = $browser->script("
                return Array.from(document.querySelectorAll('.product_pod h3 a')).map(el => el.getAttribute('title'));
            ")[0];

            dump('--- KATEGORIA: '.$categoryName.' ---');
            dump($booksInCategory);

            $this->assertNotEmpty($booksInCategory);
        });
    }
}
