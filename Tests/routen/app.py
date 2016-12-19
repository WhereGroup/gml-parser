import os

command ="ogr2ogr -f 'PostgreSQL'  PG:'dbname=gml user=ransomware host=localhost port=5432' -nln 'trlp_%s'  %s"

print "#!/bin/bash"
for root, dir, files in os.walk(u"."):
        for gmlFile in files:
                if gmlFile.endswith(".gml"):
                        print command % (gmlFile[:gmlFile.index(".gml")-1] ,gmlFile)
