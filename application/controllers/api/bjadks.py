
# coding:utf-8

import requests
import urllib.parse
import os
import sys
from bs4 import BeautifulSoup
from distutils.filelist import findall

def ifingen(g,content):
    for item in g:
        if(item == content):
            return True
    return False

def search(url, keyword):
    data = {}
    data['keyword'] = keyword
    data['selVal'] = '-1'
    data['PageIndex'] = '1'
    url_para = urllib.parse.urlencode(data)

    res = requests.get(url+'?'+url_para)
    soup = BeautifulSoup(res.text, "html.parser")
    for tag in soup.find_all('div', class_='courseListVer'):
        if len(tag.find_all('dd')) !=0 :
            dd = tag.dd
            title = dd.find('a');
            if (title and ifingen(title.stripped_strings,keyword)) :
                _rst = {}
                _rst['keyword'] = keyword
                course = tag.a
                img = course.img
                _rst['course_href'] = course['href']
                _rst['img_src'] = img['src']

                info = tag.find('p', class_='info')
                isrole = False
                isclass = False

                for content in info.stripped_strings:
                    if(isrole):
                        isrole = False
                        _rst['role'] = content
                    if(isclass):
                        isclass = False
                        _rst['class'] = content
                    if(content.find('主讲:') != -1 or content.find('主讲：') != -1):
                        isrole = True
                    elif(content.find('课时:') != -1 or content.find('课时：') != -1):
                        isclass = True
                return _rst
    return False

rst = []
if len(sys.argv)!=3:
    print(u'缺少参数')
else :
    keywords = sys.argv[1].split(',')
    path = sys.argv[2]
    for item in keywords:
        item = item.encode(sys.getfilesystemencoding(),errors='surrogateescape')
        _rst = search('https://wb.bjadks.com/Search/Index',item.decode('latin-1'))
        if _rst :
            rst.append(_rst)
    if len(rst) != 0:
        if os.path.exists(path+'template.txt'):
            f_template = open(path+'template.txt','r');
            template =  f_template.read();

            out = ''
            for item in rst:
                outstring = template
                outstring = outstring.replace('$py_img_src',item['img_src'])
                outstring = outstring.replace('$py_course_href','https://wb.bjadks.com/'+item['course_href'])
                outstring = outstring.replace('$py_class',item['class'])
                outstring = outstring.replace('$py_keyword',item['keyword'])
                outstring = outstring.replace('$py_role',item['role'])

                out = out + outstring + '\r\n'
            print(out)
    else :
        print(u'没有查询到结果')

