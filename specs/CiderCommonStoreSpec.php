<?php
namespace Cider\Spec;

\Cider\import('Cider\Common\Store');

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

    return expect($helloWorldDict)->toEqual($store->data);

  });

  it('replaces data store with Storage::$data setter', function() use ($store, $gothamVillainsDict) {

    $store->data = $gothamVillainsDict;

    return expect($helloWorldDict)->notToEqual($store->data);

  });

  it('returns size of data store with Storage::$size', function() use ($store, $helloWorldDict) {

    $helloWorldDictSize = count($helloWorldDict);

    return expect($helloWorldDictSize)->toEqual($store->size);

  });

  it('returns only keys from data store with Storage::$keys', function() use ($store, $helloWorldDict) {

    $helloWorldDictKeys = array_keys($helloWorldDict);

    return expect($helloWorldDictKeys)->toEqual($store->keys);

  });

  it('returns only values from data store with Storage::$values', function() use ($store, $helloWorldDict) {

    $helloWorldDictValues = array_values($helloWorldDict);

    return expect($helloWorldDictValues)->toEqual($store->values);

  });

});
