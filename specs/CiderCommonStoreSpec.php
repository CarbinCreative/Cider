<?php
namespace Cider\Spec;

/* @imports*/
use function \Cider\import;
use function \Cider\expect;

import('Cider\Common\Store');

describe('Cider\Common\Store', function() {

  $helloWorldDict = [
    'en' => 'Hello World',
    'se' => 'Hejsan Världen',
    'jp' => 'こんにちは世界',
    'de' => 'Hallo Welt',
    'es' => 'Hola mundo',
    'nl' => 'Hello wereld',
    'ru' => 'Здравствулте мир'
  ];

  $gothamVillainsDict = [
    'jk' => 'The Joker',
    'hq' => 'Harley Quinn',
    'en' => 'Edward Nigma'
  ];

  $store = new \Cider\Common\Store($helloWorldDict);

  beforeEach(function() use ($store, $helloWorldDict) {

    $store->data = $helloWorldDict;

  });

  it('returns data store with Store::$data getter', function() use ($store, $helloWorldDict) {

    return expect($store->data)->toEqual($helloWorldDict);

  });

  it('replaces data store with Store::$data setter', function() use ($store, $gothamVillainsDict, $helloWorldDict) {

    $store->data = $gothamVillainsDict;

    return expect($store->data)->notToEqual($helloWorldDict);

  });

  it('returns size of data store with Store::$size', function() use ($store, $helloWorldDict) {

    $helloWorldDictSize = count($helloWorldDict);

    return expect($store->size)->toEqual($helloWorldDictSize);

  });

  it('returns only keys from data store with Store::$keys', function() use ($store, $helloWorldDict) {

    $helloWorldDictKeys = array_keys($helloWorldDict);

    return expect($store->keys)->toEqual($helloWorldDictKeys);

  });

  it('returns only values from data store with Store::$values getter', function() use ($store, $helloWorldDict) {

    $helloWorldDictValues = array_values($helloWorldDict);

    return expect($store->values)->toEqual($helloWorldDictValues);

  });

  it('returns key from input value', function() use ($store) {

    return expect($store->keyOf('Hello World'))->toEqual('en');

  });

  it('returns last key from data store with Store::$firstKey getter', function() use ($store, $helloWorldDict) {

    $helloWorldDictKeys = array_keys($helloWorldDict);
    $firstKeyInHelloWorldDict = $helloWorldDictKeys[0];

    return expect($store->firstKey)->toEqual($firstKeyInHelloWorldDict);

  });

  it('returns last key from data store with Store::$lastKey getter', function() use ($store, $helloWorldDict) {

    $helloWorldDictKeys = array_keys($helloWorldDict);
    $helloWorldDictSize = count($helloWorldDict);
    $lastKeyInHelloWorldDict = $helloWorldDictKeys[$helloWorldDictSize - 1];

    return expect($store->lastKey)->toEqual($lastKeyInHelloWorldDict);

  });

  it('can check if a key exists', function() use ($store) {

    return expect($store->has('en'))->toBeTrue();

  });

  it('can merge additional stores', function() use ($store, $helloWorldDict, $gothamVillainsDict) {

    $mergedDicts = $helloWorldDict + $gothamVillainsDict;

    $store->merge($gothamVillainsDict);

    return expect($store->data)->toEqual($mergedDicts);

  });

  it('can serialize data store', function() use ($store, $gothamVillainsDict) {

    $store->data = $gothamVillainsDict;
    $jsonString = json_encode($gothamVillainsDict);

    return expect($store->serialized)->toEqual($jsonString);

  });

  it('can parameterize data store', function() use ($store, $gothamVillainsDict) {

    $store->data = $gothamVillainsDict;
    $queryString = http_build_query($gothamVillainsDict);

    return expect($store->parameterized)->toEqual($queryString);

  });

});
