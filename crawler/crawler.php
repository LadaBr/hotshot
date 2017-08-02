<?php

ini_set('max_execution_time', -1);
ini_set('memory_limit', '-1');
//header('content-type: application/xml');
$log = "log.txt";
$test = false;


function pprint($var) {
    print_r("<pre>");
    print_r($var);
    print_r("</pre>");
    
}
function getAllChildrenHref($element) {
    $children = array(

    );
    foreach ($element->childNodes as $child) {

        if ( $child->nodeName == 'a' ) {
            if ($child->getAttribute('href') != "#") {
                array_push($children, $child->getAttribute('href'));
            }
        }
        if (count($child->childNodes) > 0) {
            $children = array_merge($children,getAllChildrenHref($child));
        }
    }
    return $children;
}
function getAllChildren($element) {
    $children = array(

    );
    foreach ($element->childNodes as $child) {

        array_push($children, $child);

        if (count($child->childNodes) > 0) {
            $children = array_merge($children,getAllChildren($child));
        }
    }
    return $children;
}
function getDOMDocument($url, $query) {
    global $host, $log;
    file_put_contents($log, date('Y-m-d H:i:s')." CRAWLING: ".$url.PHP_EOL, FILE_APPEND | LOCK_EX);
    echo "CRAWLING URL: ".$url."</br>";
    $dom = new DOMDocument();
    @$dom->loadHTML(file_get_contents($url));

    $finder = new DomXPath($dom);
    $elements = $finder->query($query);
    echo $query;
    
    $children = array();
    foreach ($elements as $element) {
        $children = array_merge($children,getAllChildrenHref($element));
    }
    var_dump($children);

    return $children; 

}
function getDOMElementAttributes($p, $filter) {
    $dict = array();
    if ($p->hasAttributes()) {
        foreach ($p->attributes as $attr) {

            if (($attr->nodeName == $filter && isset($filter)) || !isset($filter)) {
                $dict['name'] = $attr->nodeName;
                $dict['value'] = $attr->nodeValue;
                //array_push($attributes, $dict);
                break;
            }
            
        }
    }
    return $dict;
    
}
function getDOMElements($url, $query) {
    $dom = new DOMDocument();
    @$dom->loadHTML(file_get_contents($url));

    $finder = new DomXPath($dom);
    $elements = $finder->query($query);
    return $elements;
}

$websites = array(
    0 => array(
        'active' => 0,
        'host' => 'http://www.dstreet.sk',
        'url_params' => '?filter=&page=1&range=9999',
        'root' => array(
            'url' => '/kategoria/p-ihned-k-odoslaniu-p/',
            'selector' => "//div[contains(@class, 'kategorie')]",
        ),
        'products' => "//div[contains(@class, 'produkty_obal')]",
        'selectors' => array(
            'name' => "//h2",
            'price' => "//div[contains(@class, 'cena')]",
            'category' => "//span[contains(@typeof, 'v:Breadcrumb')]",
            'quantity' => "//strong[contains(@id, 'dodaciaDoba')]",

            'description' => "//div[contains(@class, 'popis_tovaru')]",
            'size' => "//*[contains(@class, 'abuttonbutton')]",
            'image' => "//*[contains(@class, 'gal')]", 
        ),
        'output' => 'dstreet.xml',
    ),
    1 => array(
        'active' => 1,
        'host' => 'http://www.bolf.sk',
        'url_params' => '',
        'root' => array(
            'url' => '',
            'selector' => "//div[contains(@id, 'menu_categories2')]",
        ),
        'products' => "//div[contains(@id, 'search')]",
        'selectors' => array(
            'name' => "//*[contains(@class, 'product_info_name')]",
            'price' => "//*[contains(@class, 'projector_price_value')]",
            'category' => "//a[contains(@class, 'category')]",
            //'quantity' => "//strong[contains(@id, 'dodaciaDoba')]",

            'description' => "//div[contains(@id, 'projector-longdescription-description')]",
            'size' => "//*[contains(@class, 'select_button')]",
            'image' => "//*[contains(@class, 'projector_medium_image')]", 
        ),
        'output' => 'bolf.xml',
    ),
    2 => array(
        'active' => 0,
        'host' => 'https://www.insportline.sk',
        'url_params' => '?v=devron&page=1-6',
        'root' => array(
            'url' => '',
            'selector' => "",
        ),
        'products' => "//div[contains(@class, 'product')]",
        'selectors' => array(
            'name' => "//*[contains(@class, 'detail_title')]",
            'price' => "//strong[contains(@itemprop, 'price')]",
            'fake_price' => "//div[contains(@class, 'detail_data')]/strike",
            'category' => "//a[contains(@class, 'category')]",
            //'quantity' => "//strong[contains(@id, 'dodaciaDoba')]",

            'short_description' => "//p[contains(@class, 'desc') and contains(@itemprop, 'description')]",
            'description' => "(//div[contains(@class, 'tabs-content')])[1]",
            'size' => "//a[contains(@class, 'txt')]",
            'color' => "//div[contains(@class, 'variant_list')]/a[not(contains(@class, 'txt'))]",
            'image' => "//a[contains(@class, 'fancybox')]", 
            'parameter_title' => "//div[contains(@class, 'table')]/div[contains(@class, 'left')]",
            'parameter_value' => "//div[contains(@class, 'table')]/div[contains(@class, 'right')]",
        ),
        'output' => 'devron.xml',
    ),
);




foreach ($websites as $website) {
    if (!$website['active']) {
        continue;
    }
    if (strlen($website['root']['selector']) > 0) {
        $sitemap = getDOMDocument($website['host'].$website['root']['url'], $website['root']['selector']);
        

        foreach ($sitemap as $child) {
            
            $sitemap = array_unique(array_merge($sitemap,getDOMDocument($website['host'].$child, $website['root']['selector'])), SORT_REGULAR);
                if ($test) {
                    if (!empty($sitemap))
                        break;
                }
        }
        if ($test) {
            pprint($sitemap);
        }
    }
    else
        $sitemap = array('');
    
    pprint("SITEMAP");
    pprint($sitemap);
    $arr1 = array ($sitemap);
    file_put_contents("sitemap.json",json_encode($arr1));

    $products_sitemap = array();
    foreach ($sitemap as $url) {
        $products_sitemap = array_unique(array_merge($products_sitemap,getDOMDocument($website['host'].$url.$website['url_params'], $website['products'])), SORT_REGULAR);
        foreach ($products_sitemap as $key => $value) {
            $game_end_pos = strpos($value, 'product-slo');
            if ($game_end_pos === false) {
                unset($products_sitemap[$key]);
            } 
        }
        pprint($products_sitemap);
        if ($test) {
            if (!empty($products_sitemap))
                 break;
         }
    }

    $products = array();
    if ($test) {
        pprint($products_sitemap);
    }

    pprint("PRODUCT SITEMAP");
    pprint($products_sitemap);
    $arr1 = array ($products_sitemap);
    file_put_contents("products_sitemap.json",json_encode($arr1));

    foreach ($products_sitemap as $url) {
        $dict = array();
        //$stop = false;
        foreach ($website['selectors'] as $key => $value) {
            $elements = getDOMElements($website['host'].$url, $value);

            $array = array();

            foreach ($elements as $element) {
                if ($key == 'image') {
                    $attributes = array_unique(getDOMElementAttributes($element, 'href'), SORT_REGULAR);
                    if (!empty($attributes)) {
                        array_push($array, $attributes);
                    }
                } else if ($key == 'price' || $key == 'fake_price') {
                    $price = preg_replace('/[^0-9.,€]/',"",$element->textContent);
                    $price = preg_replace('/,/', '.', $price);
                    $price = array_filter(explode('€', $price));
                    array_push($array,  array_pop($price));
                } else if ($key == 'size' || $key == 'category') {
                    array_push($array, trim(preg_replace('/\t+/', '', $element->textContent)));
                } else if ($key == 'color') {
                    $attributes = getDOMElementAttributes($element, 'title');
                    if (!empty($attributes)) {
                        array_push($array, $attributes);
                    }
                } else if ($key == 'parameter_title') {
                    array_push($array, $element->firstChild->textContent);
                } else {
                    array_push($array, $element->textContent);
                }
            }



            $dict[$key] = $array;
            if ($test) {
                if ($key == 'size') {
                    if (count($array) > 0) {
                        $stop = true;
                    }
                }
            }
            
        }
        pprint($dict);
        array_push($products, $dict);
        if ($test) {
            if ($stop) {
                break;
            }
        }
    }


    echo "A";
    $products = array_unique($products, SORT_REGULAR);
    file_put_contents($log, date('Y-m-d H:i:s')." WTF", FILE_APPEND | LOCK_EX);
    if ($test) {
        pprint($products);
    }
    echo "B";

    pprint("PRODUCTS");
    pprint($products);

    $domDoc = new DomDocument('1.0');
    $domDoc->preserveWhiteSpace = false;
    $domDoc->formatOutput = true;
    $rootElt = $domDoc->createElement('products');
    $rootNode = $domDoc->appendChild($rootElt);

    $counter = 0;
    foreach ($products as $product) {
        $subElt = $domDoc->createElement('product');
        $subNode = $rootNode->appendChild($subElt);

        foreach ($product as $key => $arrays) {
            $subBak = $subNode;

            if (count($arrays) > 1) {
                $keyBak = $key;
                if (substr($key, -1) == 'y') {

                    $key = substr($key, 0, -1)."ie";
                }
                $subElt = $domDoc->createElement($key."s");
                $subNode = $subNode->appendChild($subElt);
                $key = $keyBak;
            }
            

            foreach ($arrays as $value) {
                pprint($value);
                if ($key == 'size') {
                    if (strlen($value) == 0) {
                        continue;
                    }
                }
                if ($key == 'image') {
                    if ($value['value'][0] != "/") {
                        $value = $website['host']."/".$value['value'];
                    } else {
                        $value = $website['host'].$value['value'];
                    }
                    
                }
                if ($key == 'color') {
                    $value = $value['value'];
                }
                $subsubsubElt = $domDoc->createElement($key);
                $subsubsubNode = $subNode->appendChild($subsubsubElt);

                $textNode = $domDoc->createCDATASection($value);
                $subsubsubNode->appendChild($textNode);
            }

            $subNode = $subBak;
            
        }
        $counter++;
        file_put_contents($log, date('Y-m-d H:i:s')." ".$counter."/".count($products).PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    $domDoc->save($website['output']);
    //echo $domDoc->saveXML();
}

//include "dstreet.php";
//include "bolf.php";


?>