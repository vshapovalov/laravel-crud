import RowWrapper from './row-wrapper';
import FieldTypes from './field-types';
import RelationTypes from './relation-types';

export default class Utils {
    static randomInteger(min, max) {
        let rand = min - 0.5 + Math.random() * (max - min + 1);

        rand = Math.round(rand);

        return rand;
    }

    static createMetaObject(meta){
        let item = {};

        for(let field in meta.fields){

            if (!meta.fields.hasOwnProperty(field)) continue;

            if (meta.fields[field].type === FieldTypes.TEXTBOX ||
                meta.fields[field].type === FieldTypes.CHECKBOX ||
                meta.fields[field].type === FieldTypes.COLORBOX ||
                meta.fields[field].type === FieldTypes.TEXTAREA ||
                meta.fields[field].type === FieldTypes.DROPDOWN ||
                meta.fields[field].type === FieldTypes.DATEPICKER ||
                meta.fields[field].type === FieldTypes.RICHEDIT ||
                meta.fields[field].type === FieldTypes.TEXTBOX ||
                meta.fields[field].type === FieldTypes.IMAGE ){
                item[field] = "";
            }

            if (meta.fields[field].type === FieldTypes.RELATION){
                if (meta.fields[field].relation.type === RelationTypes.BELONGS_TO
                    || meta.fields[field].relation.type === RelationTypes.HAS_ONE){
                    item[field] = {};
                }
                if (meta.fields[field].relation.type === RelationTypes.HAS_MANY
                    || meta.fields[field].relation.type === RelationTypes.BELONGS_TO_MANY){
                    item[field] = [];
                }
            }
        }

        return item;
    }

    static wrapItems(items){
        let wrappedRows = [];

        if (items.length){
            wrappedRows = _.map(items, (i)=> this.wrapItem(i));
        }

        return wrappedRows;
    }

    static wrapItem(item){
        return new RowWrapper(item);
    }

    static unwrapItems(wrappedRows){
        let unwrappedRows = [];

        if (wrappedRows.length){
            unwrappedRows = _.map(wrappedRows, (r)=> this.unwrapItem(r));
        }

        return unwrappedRows;
    }

    static unwrapItem(wrappedRow){
        return wrappedRow.item;
    }


    static getItemsMaxOrderByLevel(level, id, items){

        let maxOrder = 0;

        _.forEach(items, (i)=>{
            if (i.level === level && (i.id !== id) && (i.order > maxOrder)) {
                maxOrder = i.order;
            }
        });

        return maxOrder;

    }

    static getPrevTreeSiblingById(id, siblings){
        let sibling = undefined;

        siblings.forEach((s, i, arr)=>{
            if ((s.id === id) && ((i-1) >= 0)) {
                sibling = arr[i-1];
            }
        });

        return sibling;
    }

    static getNextTreeSiblingById(id, siblings){
        let sibling = undefined;

        siblings.forEach((s, i, arr)=>{
            if ((s.id === id) && ((i+1) <= arr.length-1)) {
                sibling = arr[i+1];
            }
        });

        return sibling;
    }

}

