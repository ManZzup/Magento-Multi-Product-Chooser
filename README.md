# Magento Multi Product Chooser
A product chooser that can be add used in Magento 1.9x Admin forms, with massaction (multiple product selection) support. I wrote this module since a lot of solutions available in the internet either doesn't allow multi product selection or cannot be added to an Admin form. 

What does this module do?
-------------------------
1) It can give you a Admin Form Button to show a Product Chooser widget.
2) The product chooser support selecting multiple products at once, search products, select all, select visible ... functions.

How to use
----------

1) Download and extract the files into the project directory.
2) First you need to link the Javascript to your page. So add the following into your *adminhtml layout* file.
ex: assume my module is called *mymodule* and the layout file is called *mymodule.xml*

```xml
<layout version="0.1.0">
      <mymodule_index>
        <reference name="head">
            <action method="addJs"><script>multiproductchooser/chooser.js</script></action>
        </reference>
        <your_other_stuff>....</your_other_stuff>
      </mymodule_index>
</layout>
```

3) In your Form.php, add the following to get a "Select" button in your fieldset
```php
$productConfig = array(
    'input_name'  => 'sku',
    'input_label' => $this->__('Product'),
    'button_text' => $this->__('Select...'),
    'required'    => true,
    'input_id'    => 'sku'
);
$productChooserHelper = Mage::helper('multiproductchooser/chooser');
$productChooserHelper->createProductChooser(Mage::getModel('catalog/product'), $fieldset, $productConfig);
```

In which  
*input_name*  
The name of the input element, this is the one that will be submitted with your form. Format of data is a comma seperated string of selected product ids  
ex: ```10,455,65,44```

*input label*  
Name of input obviously

*button_text*  
What does your button has to show?

*input_id*  
Keep it same as the input name

4) That's all! Now load your form and click the "Select" button. And a window will appear to select the products. Once you are done, click the "Add Products" button to add them to the form. The label will show the names of the products but what is actually submitted will be the entity ids.

Credits
-------
This module combines work of several others and fixes of my own. Following are the repos and credits for the related code goes to the respective authors.  
https://github.com/extendix/Extendix_AdminFormChooserButton  
https://github.com/dmanners/Manners_Widgets

More info from http://blog.manujith.me  

Todo
----
There's one issue with the "select all" button which actually selects all products but does not show it in the label. Will fix it soon.


