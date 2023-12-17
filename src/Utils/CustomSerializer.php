<?php

namespace App\Utils;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CustomSerializer
{

	public function objectsToArray($aObjects, $aIgnoredAttributes = array())
	{
		$normalizer = new ObjectNormalizer();

		$encoders = array(new JsonEncoder());
		$serializer = new Serializer(array($normalizer), $encoders);
		$results = $serializer->normalize($aObjects, 'json', [
			AbstractNormalizer::GROUPS => ['default'],
			AbstractNormalizer::IGNORED_ATTRIBUTES => $aIgnoredAttributes,
			AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
				if (method_exists($object, 'getId')) {
					return $object->getId();
				}
				return null;
			}
		]);
		return $results;
	}
}
