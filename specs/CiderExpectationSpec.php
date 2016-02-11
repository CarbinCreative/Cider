<?php
namespace Cider\Spec;

/* @imports*/
use function \Cider\expect;
use stdClass as DummyClassObject;

describe('Cider\Spec\Expectation', function() {

	it('expects equal', function() {

		return expect(true)->toEqual(true);

	});

	it('expects not equal', function() {

		return expect(true)->notToEqual(false);

	});

	it('expects equal any', function() {

		return expect('foo')->toEqualAny('baz', 'bar', 'foo');

	});

	it('expects not equal any', function() {

		return expect('git')->notToEqualAny('php', 'ruby', 'js');

	});

	it('expects equal all', function() {

		return expect('nom')->toEqualAll('nom', 'nom', 'nom');

	});

	it('expects not equal all', function() {

		return expect('batman')->notToEqualAll('na', 'na', 'na');

	});

	it('expects truthy', function() {

		return expect(1)->toBeTruthy();

	});

	it('expects falsy', function() {

		return expect(null)->toBeFalsy();

	});

	it('expects satisfy is_string', function() {

		return expect('Hello World')->toSatisfy('is_string');

	});

	it('expects instance of stdClass', function() {

		$dummyObject = new DummyClassObject;

		return expect($dummyObject)->instanceOf('stdClass');

	});

	it('expects type of string', function() {

		return expect('Hiya!')->typeOf('string');

	});

});
