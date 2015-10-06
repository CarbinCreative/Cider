<?php
namespace Cider\Spec;

describe('Cider sanity spec', function() {

  it('can run tests', function() {

    return expect(CIDER_ROOT_PATH)->toSatisfy('is_string');

  });

});
