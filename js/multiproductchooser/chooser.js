/**
* Original author: dmanners
* From git repo : https://github.com/dmanners/Manners_Widgets
* The original fix code was taken from the widgets.js, and additional fixes were done.
**/
varienGridMassaction = Class.create(
    varienGridMassaction,
    {
        textString: ''
    }
);

varienGridMassaction.addMethods(
    {
        onGridRowClick: function(grid, evt) {
            var tdElement = Event.findElement(evt, 'td');
            var trElement = Event.findElement(evt, 'tr');

            var trChildren = trElement.children;
            var gridName = trChildren[trChildren.length - 1].innerHTML.trim();
            if (!$(tdElement).down('input')) {
                if ($(tdElement).down('a') || $(tdElement).down('select')) {
                    return;
                }
                if (trElement.title) {
                    setLocation(trElement.title);
                }
                else {
                    var checkbox = Element.select(trElement, 'input');
                    var isInput = Event.element(evt).tagName == 'input';
                    var checked = isInput ? checkbox[0].checked : !checkbox[0].checked;

                    if (checked) {
                        this.checkedString = varienStringArray.add(checkbox[0].value, this.checkedString);
                        this.textString = varienStringArray.add(checkbox[0].value, this.textString);
                    } else {
                        this.checkedString = varienStringArray.remove(checkbox[0].value, this.checkedString);
                        this.textString = varienStringArray.remove(checkbox[0].value, this.textString);
                    }
                    this.grid.setCheckboxChecked(checkbox[0], checked);
                    this.updateCount();
                }
                return;
            }

            if (Event.element(evt).isMassactionCheckbox) {
                this.setTextValue(Event.element(evt), gridName);
                this.setCheckbox(Event.element(evt));
            } else if (checkbox = this.findCheckbox(evt)) {
                checkbox.checked = !checkbox.checked;
                this.setTextValue(checkbox, gridName);
                this.setCheckbox(checkbox);
            }
        },
        setTextValue: function(checkbox, textValue) {
            if(checkbox.checked) {
                this.textString = varienStringArray.add(textValue, this.textString);
            } else {
                this.textString = varienStringArray.remove(textValue, this.textString);
            }
        },
        initTextValue: function(textValue) {
            this.textString = textValue;
        },
        getTextValue: function() {
            return this.textString;
        },
        selectAll: function() {
            this.textString = "Selected All Products";
            this.setCheckedValues((this.useSelectAll ? this.getGridIds() : this.getCheckboxesValuesAsString()));
            this.checkCheckboxes();
            this.updateCount();
            this.clearLastChecked();
            return false;
        },
        selectVisible: function() {
            var values = this.getCheckboxesValuesAsString().split(',');
            var that = this;
            this.setCheckedValues(this.getCheckboxesValuesAsString());
            this.checkCheckboxes();
            this.updateCount();
            this.clearLastChecked();
            $$('table.data tr[title=#]').each(function(e){
                var childs = e.children;
                var check = childs[0].children[0];
                var id = childs[1].innerHTML.trim();
                var val = childs[2].innerHTML.trim();
                var hasopt = false;
                for(var j=0;j<values.length;j++){
                    if(values[j] === id){
                        hasopt = true;
                        break;
                    }
                }
                if(hasopt){
                    that.setTextValue(check,val);
                }
            });
            return false;
        },
        unselectVisible: function() {
            this.getCheckboxesValues().each(function(key){
                this.checkedString = varienStringArray.remove(key, this.checkedString);
            }.bind(this));
            this.checkCheckboxes();
            this.updateCount();
            this.clearLastChecked();
            this.textString = "";
            return false;
        },
        unselectAll: function() {
            this.setCheckedValues('');
            this.checkCheckboxes();
            this.updateCount();
            this.clearLastChecked();
            this.textString = "";
            return false;
        },
    }
);