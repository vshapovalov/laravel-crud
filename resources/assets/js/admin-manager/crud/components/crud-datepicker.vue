<template>
    <v-layout row align-center>
        <v-text-field style="cursor: pointer"
            hide-details
            readonly
            class="px-0 py-0"
            solo
            :value="value"
            @click="showPickDialog"
        ></v-text-field>
        <v-tooltip top>
            <v-btn icon  slot="activator" @click="showPickDialog" class="my-0">
                <v-icon color="accent">edit</v-icon>
            </v-btn>
            <span>{{ l18n('edit') }}</span>
        </v-tooltip>

        <v-tooltip top>
            <v-btn icon slot="activator" @click="clearDate" class="my-0">
                <v-icon color="red">clear</v-icon>
            </v-btn>
            <span>{{ l18n('clear') }}</span>
        </v-tooltip>
        <v-dialog
                persistent
                lazy
                v-model="pickDialog.active"
                :max-width="mode == 'datetime' ? '580px' : '290px'"
        >
            <v-card flat>
                <v-card-text class="py-0 px-0">
                    <div class="layout row">
                        <v-date-picker v-model="date" scrollable class="card--flat" :locale="appLocale"></v-date-picker>
                        <v-time-picker v-if="mode == 'datetime'" v-model="time" format="24hr" scrollable class="card--flat"></v-time-picker>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn flat color="success" @click="submitPickDialog">{{ l18n('save') }}</v-btn>
                    <v-btn flat color="red" @click="cancelPickDialog">{{ l18n('cancel') }}</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-layout>
</template>

<script>

    export default {
        name: 'crud-datepicker',
        model: {
            prop: "value",
            event: "change"
        },
        props: ["value", "field"],
        data: function () {
            return {
                date: null,
                time: null,
                pickDialog: {
                    active: false
                }
            }
        },
        watch:{
            value(val, oldVal){
                if (val){

                    let d, t;

                    [d, t] = val.split(' ');

                    this.date = d;

                    if ( (this.mode == 'datetime') && t)
                        this.time = t;
                }
            }
        },
        computed: {
            mode(){ return (this.field.additional && this.field.additional.mode) || 'date' },
            appLocale(){ return App.locale; }
        },
        methods: {
            emitChange(){

                if (!this.date){
                    this.$emit('change', null)
                } else {

                    let changeValue = this.date;

                    if (this.mode == 'datetime')
                        changeValue += ' ' + (this.time || '00:00') + ':00';

                    this.$emit('change', changeValue);
                }
            },
            showPickDialog(){
                this.pickDialog.active = true;
            },
            cancelPickDialog(){
                this.pickDialog.active = false;
            },
            submitPickDialog(){

                this.emitChange();

                this.cancelPickDialog();
            },
            clearDate(){
                this.date = null;
                this.time = null;

                this.emitChange();
            },
        }
    }
</script>

<style>
    .v-time-picker-title__time .v-picker__title__btn, .v-time-picker-title__time span{
        height: 56px !important;
        font-size: 56px !important;
    }
    .v-time-picker-clock__container{
        height: 286px !important;
    }
</style>