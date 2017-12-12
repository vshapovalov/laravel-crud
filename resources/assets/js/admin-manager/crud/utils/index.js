import RowWrapper from './row-wrapper';
import FieldTypes from './field-types';
import RelationTypes from './relation-types';

export default class Utils {
    static randomInteger(min, max) {
        let rand = min - 0.5 + Math.random() * (max - min + 1);

        rand = Math.round(rand);

        return rand;
    }

    static spreadJsonFields(item, fields, spreadPivot = false){

        let jsonFields = _.filter(fields, (f)=>f.json);

        _.each(jsonFields, (f)=>{

            let jPath = f.name.split('->');

            let tmpValue = item[jPath[0]] ? JSON.parse(item[jPath[0]]) : null;

            jPath.splice(0,1);

            jPath = jPath.join('.');

            item[f.name] = _.get(tmpValue, jPath, this.defaultFieldValue(f));

        });

        if (spreadPivot) {
            let jsonPivotFields = _.filter(fields, (f)=>{
                return f.type === 'relation' && f.relation.type === 'belongsToMany'
                    && f.relation.pivot && _.some(f.relation.pivot.fields, (f)=>f.json);
            });

            console.log('jsonPivotFields', jsonPivotFields);

            _.each(jsonPivotFields, (f)=>{

                console.log('field', f);

                let jsonFields = _.map(_.filter(f.relation.pivot.fields, (f)=> f.json), (mf)=>{
                    let jPath = mf.name.split('->');
                    let rootFieldName = jPath[0];

                    jPath.splice(0,1);

                    jPath = jPath.join('.');

                    return {
                      rootFieldName,
                      name: mf.name,
                      jPath
                    };
                });

                _.each(item[_.snakeCase(f.name)], (pivotItem)=>{

                    _.each(jsonFields, (jf)=>{
                        console.log('jf', jf);

                        let tmpValue = pivotItem.pivot[jf.rootFieldName] ? JSON.parse(pivotItem.pivot[jf.rootFieldName]) : null;
                        console.log('tmpValue', tmpValue);

                        pivotItem.pivot[jf.name] = _.get(tmpValue, jf.jPath, this.defaultFieldValue(jf));
                        console.log('pivotItem[f.name]', pivotItem.pivot[jf.name]);
                    });

                });

            });
        }

        return item;
    }

    static defaultFieldValue(field){
        if (field.type === FieldTypes.TEXTBOX ||
            field.type === FieldTypes.CHECKBOX ||
            field.type === FieldTypes.COLORBOX ||
            field.type === FieldTypes.TEXTAREA ||
            field.type === FieldTypes.DROPDOWN ||
            field.type === FieldTypes.DATEPICKER ||
            field.type === FieldTypes.RICHEDIT ||
            field.type === FieldTypes.TEXTBOX ||
            field.type === FieldTypes.IMAGE ){
            return "";
        }

        if (field.type === FieldTypes.RELATION){
            if (field.relation.type === RelationTypes.BELONGS_TO
                || field.relation.type === RelationTypes.HAS_ONE){
                return {};
            }
            if (field.relation.type === RelationTypes.HAS_MANY
                || field.relation.type === RelationTypes.BELONGS_TO_MANY){
                return [];
            }
        }
    }

    static createMetaObject(meta){
        let item = {};

        _.each(meta.fields, (f)=>{
            item[_.snakeCase(f.name)] = this.defaultFieldValue();
        });

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

