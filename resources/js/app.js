import "./bootstrap";

import flatpickr from "flatpickr";
import "./../../node_modules/flatpickr/dist/flatpickr.min.css";

// import pgFlatpickr from "./../../vendor/power-components/livewire-powergrid/resources/js/components/pg-flatpickr.js";
// window.pgFlatpickr = pgFlatpickr;

// import pgEditable from "./../../vendor/power-components/livewire-powergrid/resources/js/components/pg-editable.js";
// window.pgEditable  = pgEditable ;
// resources/js/app.js
// resources/js/app.js

import './../../vendor/power-components/livewire-powergrid/dist/powergrid'
// import './../../vendor/power-components/livewire-powergrid/dist/bootstrap5.css'


import { Decimal } from "decimal.js";
window.Decimal = Decimal;

import { exceljs } from "exceljs";

window.ExcelJS = exceljs;

import { read, writeFileXLSX } from "xlsx";

import Choices from "choices.js";

window.Choices = Choices;


