<?php

namespace Tomaj\Form\Renderer;

use Nette;
use Nette\Forms\Rendering\DefaultFormRenderer;
use Nette\Forms\Controls;
use Nette\Utils\Html;

class BootstrapVerticalRenderer extends DefaultFormRenderer
{
    public $wrappers = array(
        'form' => array(
            'container' => null,
        ),
        'error' => array(
            'container' => 'div class="alert alert-danger"',
            'item' => 'p',
        ),
        'group' => array(
            'container' => 'fieldset',
            'label' => 'legend',
            'description' => 'p',
        ),
        'controls' => array(
            'container' => null,
        ),
        'pair' => array(
            'container' => 'div class=form-group',
            '.required' => 'required',
            '.optional' => null,
            '.odd' => null,
            '.error' => 'has-error',
        ),
        'control' => array(
            'container' => '',
            '.odd' => null,
            'description' => 'span class=help-block',
            'requiredsuffix' => '',
            'errorcontainer' => 'span class=help-block',
            'erroritem' => '',
            '.required' => 'required',
            '.text' => 'text form-control',
            '.password' => 'text form-control',
            '.file' => 'text',
            '.select' => 'form-control',
            '.submit' => 'button',
            '.image' => 'imagebutton',
            '.button' => 'button',
        ),
        'label' => array(
            'container' => '',
            'suffix' => null,
            'requiredsuffix' => '',
        ),
        'hidden' => array(
            'container' => 'div',
        ),
    );

    /**
     * Provides complete form rendering.
     * @param  Nette\Forms\Form
     * @param  string 'begin', 'errors', 'ownerrors', 'body', 'end' or empty to render all
     * @return string
     */
    public function render(Nette\Forms\Form $form, $mode = null)
    {
        $form->getElementPrototype()->setNovalidate('novalidate');

        $usedPrimary = FALSE;
        foreach ($form->getControls() as $control) {
            if ($control instanceof Controls\Button) {
                if (strpos($control->getControlPrototype()->getClass(), 'btn') === FALSE) {
                    $control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-default');
                    $usedPrimary = true;
                }
            } elseif ($control instanceof Controls\TextBase ||
                $control instanceof Controls\SelectBox ||
                $control instanceof Controls\MultiSelectBox) {
                $control->getControlPrototype()->addClass('form-control');
            } elseif ($control instanceof Controls\Checkbox ||
                $control instanceof Controls\CheckboxList ||
                $control instanceof Controls\RadioList) {
                $control->getSeparatorPrototype()->setName('div')->addClass($control->getControlPrototype()->type);
            }
        }

        return parent::render($form, $mode);
    }

  public function renderControl(Nette\Forms\IControl $control)
  {
    $body = $this->getWrapper('control container');
    if ($this->counter % 2) {
      $body->class($this->getValue('control .odd'), TRUE);
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

    $control->setOption('rendered', TRUE);
    $el = $control->getControl();
    if ($el instanceof Html && $el->getName() === 'input') {
      $el->class($this->getValue("control .$el->type"), TRUE);
    }
    if($control instanceof Controls\SelectBox) {
      $el->class($this->getValue("control .select"), TRUE);
    }
    return $body->setHtml($el . $description . $this->renderErrors($control));
  }
}
