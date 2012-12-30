<?php
/**
 * Methods for displaying presentation data in the view.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


class View extends Object {

    public $name = null;

    public $viewPath = null;
    
    public $viewVars = array();

    public $view = null;
    
    public $ext = '.ctp';
    
    public $layout = 'default';

    public $layoutPath = null;

    public $autoLayout = true;

    public $hasRendered = false;
    
    public $request = null;

    public $output = false;

    protected $_passedVars = array(
        'viewVars', 'autoLayout', 'view', 'layout', 'name',
        'layoutPath', 'viewPath', 'request'
    );

    protected $_scripts = array();

    protected $_paths = array();


    public function __construct($controller) {
        if (is_object($controller)) {
            $count = count($this->_passedVars);
            for ($j = 0; $j < $count; $j++) {
                $var = $this->_passedVars[$j];
                $this->{$var} = $controller->{$var};
            }
        }
        $this->addPath(ROOT_ . 'main/api/view/');
        $this->addPath(ROOT_ . 'main/admin/view/');
        $this->addPath(ROOT_ . 'main/web/view/');
        $this->addPath(ROOT_ . 'main/test/view/');
        $this->addPath(ROOT_ . 'main/view/');
        parent::__construct();
    }
    

    public function element($name, $data = array()) {
        $file = $this->_getElementFilename($name);

        if ($file) {
            $element = $this->_render($file, array_merge($this->viewVars, $data));
            return $element;
        }
    }


    public function render($view = null, $layout = null) {
        if ($this->hasRendered) {
            return true;
        }
        $this->output = null;

        if ($view !== false && $viewFileName = $this->_getViewFileName($view)) {
            $this->output = $this->_render($viewFileName);
        }
        
        if ($layout === null) {
            $layout = $this->layout;
        }
        if ($this->output === false) {
            throw new HException(__("Error in view %s, got no content.", $viewFileName));
        }
        if ($layout && $this->autoLayout) {
            $this->output = $this->renderLayout($this->output, $layout);
        }
        $this->hasRendered = true;
        return $this->output;
    }

/**
 * Renders a layout. Returns output from _render(). Returns false on error.
 * Several variables are created for use in layout.
 *
 * - `title_for_layout` - A backwards compatible place holder, you should set this value if you want more control.
 * - `content_for_layout` - contains rendered view file
 * - `scripts_for_layout` - contains scripts added to header
 *
 * @param string $content_for_layout Content to render in a view, wrapped by the surrounding layout.
 * @param string $layout Layout name
 * @return mixed Rendered output, or false on error
 * @throws CakeException if there is an error in the view.
 */
    public function renderLayout($content_for_layout, $layout = null) {
        $layoutFileName = $this->_getLayoutFileName($layout);
        if (empty($layoutFileName)) {
            return $this->output;
        }

        $this->viewVars = array_merge($this->viewVars, array(
            'content_for_layout' => $content_for_layout,
            'scripts_for_layout' => implode("\n\t", $this->_scripts),
        ));

        if (!isset($this->viewVars['title_for_layout'])) {
            $this->viewVars['title_for_layout'] = $this->viewPath;
        }

        $this->output = $this->_render($layoutFileName);

        if ($this->output === false) {
            throw new HException(__("Error in layout %s, got no content.", $layoutFileName));
        }

        return $this->output;
    }


    public function getVars() {
        return array_keys($this->viewVars);
    }

    public function getVar($var) {
        if (!isset($this->viewVars[$var])) {
            return null;
        } else {
            return $this->viewVars[$var];
        }
    }

    public function addScript($name, $content = null) {
        if (empty($content)) {
            if (!in_array($name, array_values($this->_scripts))) {
                $this->_scripts[] = $name;
            }
        } else {
            $this->_scripts[$name] = $content;
        }
    }

    public function set($one, $two = null) {
        $data = null;
        if (is_array($one)) {
            if (is_array($two)) {
                $data = array_combine($one, $two);
            } else {
                $data = $one;
            }
        } else {
            $data = array($one => $two);
        }
        if ($data == null) {
            return false;
        }
        $this->viewVars = $data + $this->viewVars;
    }


    protected function _render($___viewFn) {
        extract($this->viewVars, EXTR_SKIP);
        ob_start();
        include $___viewFn;
        return ob_get_clean();
    }


    protected function _getViewFileName($name = null) {
        if ($name === null) {
            $name = $this->view;
        }

        $paths = $this->_paths;
        foreach ($paths as $path) {
            $filename = $path . $this->viewPath . DS . $name . $this->ext;
            if (file_exists($filename)) {
                return $filename;
            }
        }

        throw new MissingViewException(array('file' => $this->viewPath . DS . $name . $this->ext));
    }

    public function addPath($path) {
        $this->_paths[] = $path;
    }

    protected function _getLayoutFileName($name = null) {
        if ($name === null) {
            $name = $this->layout;
        }
        $subDir = null;
        if (!empty($this->layoutPath)) {
            $subDir = $this->layoutPath . DS;
        }

        $paths = $this->_paths;
        foreach ($paths as $path) {
            $filename = $path . 'layouts' . DS . $subDir . $name . $this->ext;
            if (file_exists($filename)) {
                return $filename;
            }
        }

        throw new MissingLayoutException(array('file' => $name . $this->ext));
    }

    protected function _getElementFileName($name) {
        $paths = $this->_paths;
        foreach ($paths as $path) {
            $filename = $path . 'elements' . DS . $name . $this->ext;
            if (file_exists($filename)) {
                return $filename;
            }
        }
        if (config('debug')) {
            throw new MissingElementException(array('file' => $name . $this->ext));
        }
    }


}
