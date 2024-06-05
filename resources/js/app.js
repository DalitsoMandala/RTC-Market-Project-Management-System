import "./bootstrap";

// import Alpine from "alpinejs";

// window.Alpine = Alpine;

// Alpine.start();
import flatpickr from "flatpickr";
window.flatpickr = flatpickr;

import { Decimal } from "decimal.js";
window.Decimal = Decimal;

import { exceljs } from "exceljs";

window.ExcelJS = exceljs;

import { read, writeFileXLSX } from "xlsx";

import Choices from "choices.js";

window.Choices = Choices;
