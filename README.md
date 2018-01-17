# CakePHP Improved Translate

# Intro

This code provides improved internationalization (i18n) to CakePHP forms and Database.

## Requirements

* CakePHP 2.9+
* jQuery

## Installation

Copy the repository files to your project creating or overwriting the existing files.

## Configuration

Step-by-step to set up CakePHP Improved Translate.

### i18n Table

Create the `i18n` table in your database.

```sql
CREATE TABLE i18n (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	locale VARCHAR(6) NOT NULL,
	model VARCHAR(255) NOT NULL,
	foreign_key INT UNSIGNED NOT NULL,
	field VARCHAR(255) NOT NULL,
	content MEDIUMTEXT NULL,
	INDEX locale (locale),
	INDEX model (model),
	INDEX row_id (foreign_key),
	INDEX field (field)
);
```

### Bootstrap File

Add this code to the bottom of your `app/Config/bootstrap.php`. Use the [ISO 639-2](http://www.loc.gov/standards/iso639-2/php/code_list.php) format for each language.

```php
define('DEFAULT_LANG', 'por');
Configure::write('Config.language', DEFAULT_LANG);
```

### Routes File

Add this code to your `app/Config/routes.php` before `Router::connect` section.

```php
Router::parseExtensions('json');
```

### AppController File

Add this code to your `app/Controller/AppController.php` file.

```php
public $components = array('Session','RequestHandler');

public function beforeFilter()
{
	// For others actions besides 'index' and 'view', use the default language to avoid errors
	if( in_array( $this->request->params['action'], array('index','view'))) {
		if( $this->Session->check('Config.language')) {
			Configure::write('Config.language', $this->Session->read('Config.language'));
		}
	}
}
```

### LanguagesController File

Configure the allowed languages in `app/Controller/LanguagesController.php` in the `$_validLanguages` property. Use the [ISO 639-2](http://www.loc.gov/standards/iso639-2/php/code_list.php) format.

```php
protected $_validLanguages = array('por','eng','spa');
```

### Model File

For each Model with fields to be translated, set the `ImprovedTranslate` Behavior. Replace `field1` and `field2` with the corresponding table colums, like `title` or `description`.

```php
public $actsAs = array(
	'ImprovedTranslate' => array('field1','field2')
);
```

### Form File

In each form, add form fields following the syntax: `Translation.NAME.LANGUAGE`. For default language, don't use `Translation.` prefix, just the column name.

```php
echo $this->Form->input('title', array('label' => 'Portuguese Title'));
echo $this->Form->input('Translation.title.eng', array('label' => 'English Title'));
echo $this->Form->input('Translation.title.spa', array('label' => 'Spanish Title'));
```

### Controller File

#### add() method

Use `saveMany` instead `save`

```php
// $this->Model->save($this->request->data)
$this->Model->saveMany(array($this->request->data))
```

#### edit() method

Make these changes in `edit` method.

```php
$this->Model->bindAllTranslations(); // add this line

$foo = $this->Model->findById($id);

if (!$foo) {
	throw new NotFoundException(__('Invalid'));
}

$this->Model->setTranslationFields($foo); // add this line

if ($this->request->is(array('post', 'put'))) {

	// $this->Model->id = $id;
	$this->request->data('Model.id', $id);

	// if ($this->Model->save($this->request->data)) {
	if ($this->Model->saveMany(array($this->request->data))) {
		$this->Flash->success(__('Your data has been updated.'));
		return $this->redirect(array('action' => 'index'));
	}

	$this->Flash->error(__('Unable to update your data.'));
}

if (!$this->request->data) {
	$this->request->data = $foo;
}
```

#### view() method

Use `associatedTranslate` to get translated associated data, like Post's Categories once CakePHP don't do it by default.

```php
$foo = $this->Model->findById($id);
$this->Model->associatedTranslate($foo); // add this line
```

### i18n JS File

Insert `jQuery` and the `app/webroot/js/i18n.js` to your HTML file. Put this code in your `app/View/Layouts/default.ctp` file.

```php
$this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js', array('inline' => false));
$this->Html->script('i18n', array('inline' => false));
// ...
echo $this->fetch('script'); // inside the <head> tag
```

### Set language buttons

For each language button, create a link, changing the parameter following [ISO 639-2](http://www.loc.gov/standards/iso639-2/php/code_list.php) format. In this case we used `eng` to English.

```php
echo $this->Html->link(
	'English',
	'/languages/change/eng',
	array(
		'class' => 'js-choose-language'
	)
);
```
