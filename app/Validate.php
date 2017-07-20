<?php

namespace App;

use Particle\Validator\Rule\Alnum;
use Particle\Validator\Rule\Between;
use Particle\Validator\Rule\LengthBetween;
use Particle\Validator\Rule\NotEmpty;
use Particle\Validator\Validator;

class Validate extends Validator
{
    public static function usingSchema($data, \Closure $schema)
    {
        $validator = new self();
        $schema($validator);

        return $validator->validate($data);
    }

    final public function __construct()
    {
        parent::__construct();

        $this->overwriteDefaultMessages([
            Alnum::NOT_ALNUM         => '{{ name }}은(는) 숫자 및 영문자로만 구성 될 수 있습니다.',
            Between::TOO_BIG         => '{{ name }}은(는) {{ max }}보다 작거나 같아야 합니다.',
            Between::TOO_SMALL       => '{{ name }}은(는) {{ min }}보다 크거나 같아야 합니다.',
            NotEmpty::EMPTY_VALUE    => '{{ name }}은(는) 비워서는 안됩니다.',
            LengthBetween::TOO_LONG  => '{{ name }}은 {{ max }}자 이하이어야 합니다.',
            LengthBetween::TOO_SHORT => '{{ name }}은 {{ min }}자 이상이어야 합니다.'
        ]);
    }
}