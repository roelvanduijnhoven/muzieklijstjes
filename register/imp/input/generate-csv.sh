rm -f *.csv 
xlsx2csv -s 2 -i -d "tab" lijstenB.xlsx lijstenB.csv
xlsx2csv -s 1 -i -d "tab" lijstenI.xlsx lijstenI.csv
xlsx2csv -s 1 -i -d "tab" recensenten.xlsx recensenten.csv
xlsx2csv -s 3 -i -d "tab" algemeen.xlsx algemeen.csv
xlsx2csv -s 1 -i -d "tab" rubrieken.xlsx rubrieken.csv
sed -i '1d' lijstenB.csv
sed -i '1d' lijstenI.csv
sed -i '1d' algemeen.csv
sed -i '1d' recensenten.csv
