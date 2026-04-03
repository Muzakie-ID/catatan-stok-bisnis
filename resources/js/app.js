import './bootstrap';
import { BrowserMultiFormatReader } from '@zxing/browser';
import { NotFoundException } from '@zxing/library';

window.BrowserMultiFormatReader = BrowserMultiFormatReader;
window.NotFoundException = NotFoundException;
