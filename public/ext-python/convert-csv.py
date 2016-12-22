
#!/usr/bin/env python
import sys
import xlrd
import csv
import os
import os.path

class Xlsx_Conversion_Api_Manager(object):

     def run(self, xlsx_file, file_path):
            wb = xlrd.open_workbook(xlsx_file)
            sh = wb.sheet_by_name('Promotions')
            your_csv_file = open(file_path, 'wb')
            wr = csv.writer(your_csv_file)
            for rownum in xrange(sh.nrows):
                wr.writerow(sh.row_values(rownum))
            your_csv_file.close()
            if os.path.isfile(file_path) and os.access(file_path, os.R_OK):
                print True
            else:
                print False

if __name__ == '__main__':
        xlsx_file = sys.argv[1]
        csv_output_file = sys.argv[2]
        Xlsx_Conversion_Api_Manager().run(xlsx_file, csv_output_file)
