import "./bootstrap";

import flatpickr from "flatpickr";
import "./../../node_modules/flatpickr/dist/flatpickr.min.css";



import "./../../vendor/power-components/livewire-powergrid/dist/powergrid";
// import './../../vendor/power-components/livewire-powergrid/dist/bootstrap5.css'

import { Decimal } from "decimal.js";
window.Decimal = Decimal;

import { exceljs } from "exceljs";

window.ExcelJS = exceljs;

import { read, writeFileXLSX } from "xlsx";

import Choices from "choices.js";

window.Choices = Choices;

import draftObject from "./alpine/formDraft";

window.draftObject = draftObject;

const SystemColors = [
    "#FFA239",

    "#DE8F5F",
    "#FE7743",

    "#FF5656",
];
window.SystemColors = SystemColors;

import { jsPDF } from "jspdf";
import {autoTable} from "jspdf-autotable";

window.jsPDF = jsPDF;
window.autoTable = autoTable;

// resources/js/app.js

import TomSelect from "tom-select";
window.TomSelect = TomSelect;
