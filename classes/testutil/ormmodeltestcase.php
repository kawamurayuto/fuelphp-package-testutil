<?php

namespace TestUtil;

/**
 * OrmModelTestCase
 *
 * @author kawamurayuto
 */
class OrmModelTestCase extends DbTestCase
{

    protected $model_name_pattern = '/(.+)Test$/';
    protected static $cached_validations = [];

    /**
     * 入力値検証の定義
     * 
     *  protected $validations = [
     *      'create' => [ // バリデーションパターン名
     *          [ // テストデータ
     *              [ // 入力値
     *                  'field1' => ['str_repeat', '*', 10], // callable な関数も可能
     *                  'field2' => 2,
     *              ],
     *              true, // 期待する結果
     *          ],
     *          [ ... ],
     *      ]
     *  ];
     * 
     * @link http://111.171.222.187/issues/422
     * @var array
     */
    protected $validations = [];

    public function dataValidation()
    {
        $data = [];

        foreach ($this->validations as $name => $validation) {
            foreach ($validation as $args) {
                list($values, $result) = $args;

                foreach ($values as $field => $value) {
                    if ($this->hasCallback($value)) {
                        $values[$field] = $this->applyCallback($value);
                    }
                }

                $data[] = [$name, $values, $result];
            }
        }

        return $data;
    }

    /**
     * バリデーションの共通テスト
     * 
     * @dataProvider dataValidation
     */
    public function testValidation($name, $data, $result)
    {
        /*
         * @var \Fuel\Core\Validation
         */
        $validation;

        if (isset(static::$cached_validations[$name])) {
            $validation = static::$cached_validations[$name];
        } else {
            $validation = call_user_func([$this->model_name, 'validate'], $name);
            static::$cached_validations[$name] = $validation;
        }

        $this->assertEquals($result, $validation->run($data));

        if ($result === false) {

            /* エラーメッセージが出ているか */
            foreach ($validation->error() as $error) {
                $message = $error->get_message();
                $this->assertNotEmpty($message);
            }
        }
    }

    protected function setUp()
    {
        parent::setUp();
        $this->model_name = preg_replace($this->model_name_pattern, '$1', get_class($this));
    }

    private function applyCallback(array $data)
    {
        $callback = array_shift($data);
        $args = $data;

        foreach ($args as $i => $arg) {
            if ($this->hasCallback($arg)) {
                $args[$i] = $this->applyCallback($arg);
            }
        }

        return call_user_func_array($callback, $args);
    }

    private function hasCallback($data)
    {
        return is_array($data) && is_callable(reset($data));
    }

}
