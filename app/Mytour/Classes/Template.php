<?php
namespace App\Mytour\Classes;

use Form;

class Template
{
    /**
     * @return input:text
     *
     */
    public static function text(array $option = array())
    {
        $name  = array_get($option, 'name', '');
        $label = array_get($option, 'label', '');
        $value = array_get($option, 'value', null);
        $id    = array_get($option, 'id', '');
        $class = array_get($option, 'class', '');

        return "<div class='form-group'>" . Form::label($name, $label) . Form::text($name, $value, array('id' => $id, 'class' => 'form-control ' . $class . '', 'placeholder' => $label)) . "</div>";
    }

    /**
     * @return input:select
     *
     */
    public static function select(array $option = array())
    {
        $name  = array_get($option, 'name', '');
        $label = array_get($option, 'label', '');
        $array = array_Get($option, 'array', array());
        $value = array_get($option, 'value', null);
        $id    = array_get($option, 'id', '');
        $class = array_get($option, 'class', '');

        return "<div class='form-group'>" . Form::label($name, $label) . Form::select($name, $array, $value, array('id' => $id, 'class' => 'form-control ' . $class . '')) . "</div>";
    }

    /**
     * @return input:files
     *
     */
    public static function files(array $option = array())
    {
        $name  = array_get($option, 'name', '');
        $label = array_get($option, 'label', '');
        $id    = array_get($option, 'id', '');
        $class = array_get($option, 'class', '');
        $help  = array_get($option, 'help', '');
        return " <div class='form-group'>" . Form::label($name, $label) . Form::file($name, array('id' => '' . $id . '', 'class' => '' . $class . '')) . " <p class='help-block'>" . $help . "</p></div>";
    }

    /**
     * @return input:checkbox
     *
     */
    public static function checkbox(array $option = array())
    {
        $name  = array_get($option, 'name', '');
        $value = array_get($option, 'value', null);
        $id    = array_get($option, 'id', '');
        $class = array_get($option, 'class', '');
        $label = array_get($option, 'label', '');
        $check = array_get($option, 'check', null);
        return " <div class='form-group'> <label>" . Form::checkbox($name, $value, $check, array('class' => $class, 'id' => $id)) . '&nbsp;&nbsp;' . $label . "</label> </div>";
    }

    /**
     * @return input:textarea
     *
     */
    public static function textarea(array $option = array())
    {
        $name  = array_get($option, 'name', '');
        $label = array_get($option, 'label', '');
        $value = array_get($option, 'value', null);
        $id    = array_get($option, 'id', '');
        $class = array_get($option, 'class', '');
        $rows  = array_get($option, 'rows', 3);
        $cols  = array_get($option, 'cols', 10);

        return "<div class='form-group'>" . Form::label($name, $label) . Form::textarea($name, $value, array('id' => $id, 'rows' => $rows, 'cols' => $cols, 'class' => 'form-control ' . $class . '', 'placeholder' => $label)) . "</div>";
    }

    /**
     * @return input:submit
     *
     */
    public static function submit(array $option = array())
    {
        $name  = array_get($option, 'name', '');
        $id    = array_get($option, 'id', '');
        $class = array_get($option, 'class', '');

        return "<div class='form-group'>" . Form::submit($name, array('id' => '' . $id . '', 'class' => 'btn btn-primary ' . $class . '')) . "</div>";
    }

    /**
     * @return error validation
     */
    public static function error(array $data = array())
    {
        $data_return = " <div class='alert alert-danger'> <ul>";
        foreach ($data as $key => $value) {
            $data_return .= " <li>" . $value . "</li>";
        }
        $data_return .= "</ul> </div>";

        return $data_return;
    }

    /**
     * status after submit form
     */
    public static function status($status = '')
    {
        return "<div class='alert alert-success'>" . $status . "</div>";
    }

    /**
     * Button delete record
     */
    public static function delete($url = '')
    {
        return Form::open(['method' => 'DELETE', 'url' => $url]) .
        "<button class='btn btn-danger btn-sm remove' type='submit' onclick='return confirm(\"Bạn chắc chắn muốn xóa bản ghi này!\");'>
        <i class='fa fa-trash-o'></i></button>"
        . Form::close();
    }

    /**
     * Button update record
     */
    public static function update($heft = '')
    {
        return "<a class='btn btn-primary btn-sm' href = '$heft'> <i class='fa fa-edit'></i> </a >";

    }

    /**
     * Action Active
     */
    public static function active(array $option = array())
    {
        $data_href = array_get($option, 'href', '');
        $src       = array_get($option, 'src', '');
        return "<a onclick='updateStatus(this)' data-href=" . $data_href . "> <img src=" . $src . "> </a >";
    }
}