<?php

namespace App;

use ArrayAccess;

class Flash
{
    protected $storage;

    public function __construct(ArrayAccess $storage = null)
    {
        if (!$storage) {
            if (!array_key_exists('__flash', $_SESSION)) {
                $_SESSION['__flash'] = [];
            }
            $storage = &$_SESSION['__flash'];
        }
        $this->storage = &$storage;
    }

    public function extend(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }

    public function has($key)
    {
        return array_key_exists($key, $this->storage);
    }

    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }
        $value = $this->storage[$key];
        $this->destroy($key);

        return $value;
    }

    public function set($key, $value)
    {
        $this->storage[$key] = $value;
        return $this;
    }

    public function destroy($key)
    {
        unset($this->storage[$key]);
    }

    public function count()
    {
        return count($this->storage);
    }

    public function setErrors(array $errors)
    {
        $this->set('__errors', $errors + $this->getErrors());
        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->get('__errors', []);
    }

    public function setInput(array $input)
    {
        $this->set('__inputs', $input + $this->getInput());
        return $this;
    }

    public function getInput()
    {
        return $this->get('__inputs', []);
    }
}