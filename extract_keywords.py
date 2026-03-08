import sys
import docx

skills=[
"python","sql","java","html","css",
"javascript","php","machine learning",
"data analysis","iot"
]

file=sys.argv[0]

doc=docx.Document(file)

text=""

for para in doc.paragraphs:
    text+=para.text.lower()

found=[]

for skill in skills:
    if skill in text:
        found.append(skill)

print(",".join(found))
