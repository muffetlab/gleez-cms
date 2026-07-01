<?php
/**
 * Menu Widget class
 *
 * @package    Gleez\Widget
 * @author     Sandeep Sangamreddi - Gleez
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Widget_Static extends Widget {

	public function info(){}
	public function form(){}
	public function save(array $post){}
	public function delete(array $post){}

    /**
     * @throws View_Exception
     * @throws Kohana_Exception
     */
    public function render(): string
    {
		return View::factory('widgets/static')
			->set(array(
                'title' => HTML::chars($this->widget->title),
					'content' => Text::markup($this->widget->body, $this->widget->format)
			))
			->render();
	}

}