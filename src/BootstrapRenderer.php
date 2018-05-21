<?php

namespace Tomaj\Form\Renderer;

use Nette;
use Nette\Forms\Controls;
use Nette\Forms\Rendering\DefaultFormRenderer;
use Nette\Utils\Html;

class BootstrapRenderer extends DefaultFormRenderer {

  public $wrappers = [
    'form'     => [
      'container' => null,
    ],
    'error'    => [
      'container' => 'div class="alert alert-danger"',
      'item'      => 'p',
    ],
    'group'    => [
      'container'   => 'fieldset',
      'label'       => 'legend',
      'description' => 'p',
    ],
    'controls' => [
      'container' => 'div',
    ],
    'pair'     => [
      'container' => 'div class=form-group',
      '.required' => 'required',
      '.optional' => null,
      '.odd'      => null,
      '.error'    => 'has-error',
    ],
    'control'  => [
      'container'      => 'div class=col-sm-9',
      '.odd'           => null,
      'description'    => 'span class=help-block',
      'requiredsuffix' => '',
      'errorcontainer' => 'span class=help-block',
      'erroritem'      => '',
      '.required'      => 'required',
      '.text'          => 'text form-control',
      '.email'         => 'text form-control',
      '.password'      => 'text form-control',
      '.select'        => 'text form-control',
      '.file'          => 'text',
      '.submit'        => 'btn',
      '.image'         => 'imagebutton',
      '.button'        => 'button',
    ],
    'label'    => [
      'container'      => 'div class="col-sm-3 control-label"',
      'suffix'         => null,
      'requiredsuffix' => '',
    ],
    'hidden'   => [
      'container' => 'div',
    ],
  ];

  /**
   * Provides complete form rendering.
   * @param  Nette\Forms\Form
   * @param  string 'begin', 'errors', 'ownerrors', 'body', 'end' or empty to render all
   * @return string
   */
  public function render (Nette\Forms\Form $form, $mode = null) {
    if ($this->form !== $form) {
      $this->form = $form;
    }

    $form->getElementPrototype()->addClass('form-horizontal');
    $form->getElementPrototype()->setNovalidate('novalidate');
    foreach ($form->getControls() as $control) {
      if ($control instanceof Controls\Button) {
        if (strpos($control->getControlPrototype()->getClass(), 'btn') === false) {
          $control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-default');
          $usedPrimary = true;
        }
      } elseif ($control instanceof Controls\TextBase ||
        $control instanceof Controls\SelectBox ||
        $control instanceof Controls\MultiSelectBox
      ) {
        $control->getControlPrototype()->addClass('form-control');
      } elseif ($control instanceof Controls\Checkbox ||
        $control instanceof Controls\CheckboxList ||
        $control instanceof Controls\RadioList
      ) {
        $control->getSeparatorPrototype()->setName('div')->addClass($control->getControlPrototype()->type);
      }
    }

    return parent::render($form, $mode);
  }

  public function renderControl (Nette\Forms\IControl $control) {
    $form = $control->getForm();
    if ($this->form !== $form) {
      $this->form = $form;
    }

    $body = $this->getWrapper('control container');
    if ($this->counter % 2) {
      $body->class($this->getValue('control .odd'), true);
    }

    $description = $control->getOption('description');
    if ($description instanceof Html) {
      $description = ' ' . $description;
    } elseif (is_string($description)) {
      $description = ' ' . $this->getWrapper('control description')->setText($control->translate($description));
    } else {
      $description = '';
    }

    if ($control->isRequired()) {
      $description = $this->getValue('control requiredsuffix') . $description;
    }

    $control->setOption('rendered', true);
    $el = $control->getControl();
    if ($el instanceof Html && $el->getName() === 'input') {
      $el->class($this->getValue("control .$el->type"), true);
    }
    if ($control instanceof Controls\SelectBox) {
      $el->class($this->getValue("control .select"), true);
    }
    if($control instanceof Controls\TextArea) {
      $el->class('form-control');
    }

    return $body->setHtml($el . $description . $this->renderErrors($control));
  }
}
