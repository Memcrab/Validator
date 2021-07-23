<?php declare(strict_types=1);


namespace memCrab\Validator;

use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

class Validator
{
    private array $bodyConditions = [];
    private array $routeConditions = [];

    protected \stdClass $routeData;
    protected \stdClass $data;

    public function __construct(\stdClass $routeData, \stdClass $data)
    {
        $this->routeData = $routeData;
        $this->data = $data;
    }

    public static function init(string $validatorsDirectory)
    {
        $dir = new \DirectoryIterator($validatorsDirectory);
        /** @var \DirectoryIterator $fileinfo */
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                require_once $fileinfo->getPathname();
            }
        }
    }

    public function addBodyRule(string $name, v $rule, string $errorMessage, $errorCode, bool $required = true): self
    {
        $this->bodyConditions[$name]['rules'][] = $this->setCondition($rule, $errorMessage, $errorCode, $required);
        return $this;
    }

    public function addRouteRule(string $name, v $rule, string $errorMessage, $errorCode, bool $required = true): self
    {
        $this->routeConditions[$name]['rules'][] = $this->setCondition($rule, $errorMessage, $errorCode, $required);
        return $this;
    }

    private function setCondition(v $rule, string $errorMessage, $errorCode, bool $required): array
    {
        return [
            'v' => $rule,
            'errorMessage' => $errorMessage,
            'errorCode' => $errorCode,
            'required' => $required
        ];
    }

    public function validate()
    {
        $this->checkConditions($this->bodyConditions, $this->data);
        $this->checkConditions($this->routeConditions, $this->routeData);
    }

    private function checkConditions(array $conditions, \stdClass $data)
    {
        foreach ($conditions as $name => $condition) {
            foreach ($condition['rules'] as $rule) {
                try {
                    if (!property_exists($data, $name) && $rule['required']) {
                        throw new ValidatorException("Field $name not isset", 400100);
                    } elseif (!property_exists($data, $name) && !$rule['required']) {
                        continue;
                    }
                    $rule['v']->check($data->$name);
                } catch (ValidationException $e) {
                    throw new ValidatorException($rule['errorMessage'], $rule['errorCode']);
                }
            }
        }
    }

    public function getBodyConditions(): array
    {
        return $this->bodyConditions;
    }

    public function getRouteConditions(): array
    {
        return $this->routeConditions;
    }
}