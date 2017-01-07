
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
                start_date = sh.row_values(rownum)[2]
                end_date = sh.row_values(rownum)[3]
                if isinstance( start_date, float) or isinstance( start_date, int ):
                    year, month, day, hour, minute, sec = xlrd.xldate_as_tuple(start_date, wb.datemode)
                    start_date = "%04d-%02d-%02d" % (year, month, day)
                if isinstance( end_date, float) or isinstance( end_date, int ):
                    year, month, day, hour, minute, sec = xlrd.xldate_as_tuple(end_date, wb.datemode)
                    end_date = "%04d-%02d-%02d" % (year, month, day)
                wr.writerow(sh.row_values(rownum)[:2] + [start_date] + [end_date] + sh.row_values(rownum)[4:])
            your_csv_file.close()
            if os.path.isfile(file_path) and os.access(file_path, os.R_OK):
                print True
            else:
                print False

            

if __name__ == '__main__':
        xlsx_file = sys.argv[1]
        csv_output_file = sys.argv[2]
        Xlsx_Conversion_Api_Manager().run(xlsx_file, csv_output_file)
