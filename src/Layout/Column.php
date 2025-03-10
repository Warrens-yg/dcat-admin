<?php

namespace Dcat\Admin\Layout;

use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Renderable;

class Column implements Renderable
{
    /**
     * grid system prefix width.
     *
     * @var array
     */
    protected $width = [];

    /**
     * @var array
     */
    protected $contents = [];

    /**
     * Column constructor.
     *
     * @param $content
     * @param int $width
     */
    public function __construct($content, $width = 12, $style = '')
    {
        if ($content instanceof \Closure) {
            call_user_func($content, $this);
        } else {
            $this->append($content);
        }

        ///// set width.
        // if null, or $this->width is empty array, set as "md" => "12"
        if (is_null($width) || (is_array($width) && count($width) === 0)) {
            $this->width['md'] = 12;
        }
        // $this->width is number(old version), set as "md" => $width
        elseif (is_numeric($width)) {
            $this->width['md'] = $width;
        } else {
            $this->width = $width;
        }
        
        $this->style = $style;
    }

    /**
     * Append content to column.
     *
     * @param $content
     *
     * @return $this
     */
    public function append($content)
    {
        $this->contents[] = $content;

        return $this;
    }

    /**
     * Add a row for column.
     *
     * @param $content
     *
     * @return Column
     */
    public function row($content)
    {
        if (! $content instanceof \Closure) {
            $row = new Row($content);
        } else {
            $row = new Row();

            call_user_func($content, $row);
        }

        return $this->append($row);
    }

    /**
     * Build column html.
     *
     * @return string
     */
    public function render()
    {
        $html = $this->startColumn();

        foreach ($this->contents as $content) {
            $html .= Helper::render($content);
        }

        return $html.$this->endColumn();
    }

    /**
     * Start column.
     *
     * @return string
     */
    protected function startColumn()
    {
        // get class name using width array
        $classnName = collect($this->width)->map(function ($value, $key) {
            return "col-$key-$value";
        })->implode(' ');

        return "<div class=\"{$classnName}\" style=\"{$this->style}\">";
    }

    /**
     * End column.
     *
     * @return string
     */
    protected function endColumn()
    {
        return '</div>';
    }
}
